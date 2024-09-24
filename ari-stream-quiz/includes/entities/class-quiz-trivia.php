<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Wordpress\Helper as WP_Helper;

class Quiz_Trivia extends Quiz {
    public $quiz_type = ARISTREAMQUIZ_QUIZTYPE_TRIVIA;

    public $result_templates = array();

    protected $json_fields = array( 'questions', 'result_templates' );

    public function bind( $data, $ignore = array() ) {
        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $db = $this->db;
        $is_new = $this->is_new();

        $result = parent::store( $force_insert );

        if ( ! $result ) {
            return $result;
        }

        if ( ! $is_new ) {
            $query = sprintf(
                'DELETE Q,A FROM `%1$sasq_questions` Q LEFT JOIN `%1$sasq_answers` A ON Q.question_id = A.question_id WHERE Q.quiz_id = %2$d',
                $db->prefix,
                $this->quiz_id
            );
            $db->query( $query );

            $query = sprintf(
                'DELETE FROM `%1$sasq_result_templates` WHERE quiz_id = %2$d',
                $db->prefix,
                $this->quiz_id
            );
            $db->query( $query );
        }

        $order = 0;
        $filtered_questions = array();
        foreach ( $this->questions as $question ) {
            $question_entity = new Question( $db );
            $question_entity->bind( $question );

            if ( $question_entity->is_empty() ) {
                continue;
            }

            $question_entity->quiz_id = $this->quiz_id;

            if ( $is_new ) {
                $question->question_id = 0;
            }

            if ( $question_entity->validate() ) {
                $question_entity->question_order = $order;
                $question_entity->store( true );

                ++$order;
                $filtered_questions[] = $question;
            }
        }

        $this->questions = $filtered_questions;

        foreach ( $this->result_templates as $result_template ) {
            $template_entity = new Result_Template( $db );
            $template_entity->bind( $result_template );
            $template_entity->quiz_id = $this->quiz_id;

            if ( $is_new ) {
                $template_entity->template_id = 0;
            }

            if ( $template_entity->is_empty() ) {
                continue;
            }

            if ( $template_entity->validate() ) {
                $template_entity->store( true );
            }
        }

        if ( count( $this->questions ) != $this->question_count ) {
            $this->question_count = count( $this->questions );

            $this->db->update(
                $this->db_tbl,
                array(
                    'question_count' => $this->question_count,
                ),
                array(
                    'quiz_id' => $this->quiz_id,
                )
            );
        }

        return true;
    }

    protected function populate_entity( $images ) {
        $db = $this->db;
        $questions = $db->get_results(
            sprintf(
                'SELECT Q.* FROM `%1$sasq_questions` Q WHERE Q.quiz_id = %2$d ORDER BY Q.question_order ASC',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT_K
        );

        $answers = $db->get_results(
            sprintf(
                'SELECT A.* FROM `%1$sasq_answers` A WHERE A.quiz_id = %2$d ORDER BY A.question_id,A.answer_order ASC',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT
        );

        $question_answers = array();

        if ( is_array( $answers ) ) {
            foreach ( $answers as $answer ) {
                $question_id = $answer->question_id;

                if ( ! isset( $question_answers[ $question_id ] ) ) {
                    $question_answers[ $question_id ] = array();
                }

                if ( $answer->image_id > 0 ) {
                    $answer->image = $this->get_image( $answer->image_id, $images );
                }

                $question_answers[ $question_id ][ $answer->answer_id ] = $answer;
            }
        }

        if ( is_array( $questions ) ) {
            foreach ( $questions as $question ) {
                $question_id = $question->question_id;

                if ( isset( $question_answers[ $question_id ] ) ) {
                    $question->answers = $question_answers[ $question_id ];
                } else {
					$question->answers = array();
                }

                if ( $question->image_id > 0 ) {
                    $question->image = $this->get_image( $question->image_id, $images );
                }
            }
        } else {
            $questions = array();
        }

        $this->questions = $questions;

        $result_templates = $db->get_results(
            sprintf(
                'SELECT T.* FROM `%1$sasq_result_templates` T WHERE T.quiz_id = %2$d ORDER BY T.end_point ASC',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT
        );

        if ( is_array( $result_templates ) ) {
            foreach ( $result_templates as $result_template ) {
                if ( $result_template->image_id > 0 ) {
                    $result_template->image = $this->get_image( $result_template->image_id, $images );
                }
            }
        } else {
            $result_templates = array();
        }

        $this->result_templates = $result_templates;

        return true;
    }

    protected function prepare_copy( $quiz_copy ) {
        $new_result_templates = array();
        $new_questions = array();

        foreach ( $this->result_templates as $result_template ) {
            $new_result_template = clone $result_template;

            $new_result_template->template_id = 0;
            $new_result_template->quiz_id = 0;

            $new_result_templates[] = $new_result_template;
        }

        foreach ( $this->questions as $question ) {
            $new_question = clone $question;

            $new_question->question_id = 0;
            $new_question->quiz_id = 0;

            foreach ( $new_question->answers as $answer ) {
                $answer->answer_id = 0;
                $answer->question_id = 0;
                $answer->quiz_id = 0;
            }

            $new_questions[] = $new_question;
        }

        $quiz_copy->questions = $new_questions;
        $quiz_copy->result_templates = $new_result_templates;
    }

    protected function get_images() {
        $id_list = $this->db->get_col(
            sprintf(
                '(SELECT quiz_image_id FROM `%1$sasq_quizzes` WHERE quiz_id = %2$d AND quiz_image_id > 0)
                UNION
                (SELECT image_id FROM `%1$sasq_questions` WHERE quiz_id = %2$d AND image_id > 0)
                UNION
                (SELECT image_id FROM `%1$sasq_answers` WHERE quiz_id = %2$d AND image_id > 0)
                UNION
                (SELECT image_id FROM `%1$sasq_result_templates` WHERE quiz_id = %2$d AND image_id > 0)',
                $this->db->prefix,
                $this->quiz_id
            )
        );

        $images = WP_Helper::get_attachment_list( $id_list, $this->image_size );

        return $images;
    }

    public function validate() {
        $is_valid = parent::validate();

        if ( ! $is_valid ) {
            return $is_valid;
        }

        return true;
    }
}
