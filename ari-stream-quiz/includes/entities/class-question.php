<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity;
use Ari\Wordpress\Kses_Helper as WP_Kses_Helper;

class Question extends Entity {
    public $question_id;

    public $quiz_id;

    public $question_title;

    public $image_id;

    public $image = array();

    public $question_order;

    public $question_explanation = '';

    public $show_explanation = 0;

    public $answers = array();

    protected $bool_fields = array(
        'show_explanation',
    );

    protected $fields_to_filter = array(
        'question_title',

        'question_explanation',
    );

    public function __construct( $db ) {
        parent::__construct( 'asq_questions', 'question_id', $db );
    }

    public function store( $force_insert = false ) {
        foreach ( $this->fields_to_filter as $field_to_filter ) {
            $this->$field_to_filter = WP_Kses_Helper::clean_by_cap( $this->$field_to_filter );
        }

        $result = parent::store( $force_insert );

        if ( ! $result ) {
            return $result;
        }

        $is_new = $this->is_new();

        $db = $this->db;
        $answers = array();

        $filtered_answers = array();
        $answer_order = 0;
        foreach ( $this->answers as $answer ) {
            if ( $this->is_empty_answer( $answer ) ) {
                continue;
            }

            $answer_data = $db->prepare(
                join(
                    ',',
                    array(
                        '%d', // 1 - question_id
                        '%d', // 2 - quiz_id
                        '%d', // 3 - image_id
                        '%s', // 4 - answer_title
                        '%d', // 5 - answer_correct
                        '%d', // 6 - answer_order,
                        '%s', // 7 - answer_guid
                    )
                ),
                $this->question_id, // 1
                $this->quiz_id, // 2
                $answer->image_id, // 3
                WP_Kses_Helper::clean_by_cap( $answer->answer_title ), // 4
                isset( $answer->answer_correct ) ? $answer->answer_correct : 0, // 5
                $answer_order, // 6
                isset( $answer->answer_guid ) ? $answer->answer_guid : '' // 7
            );

            $answer_data = ( empty( $answer->answer_id ) ? 'NULL' : $db->prepare( '%d', array( $answer->answer_id ) ) ) . ',' . $answer_data;
            $answer_data = '(' . $answer_data . ')';
            $answers[] = $answer_data;

            ++$answer_order;
            $filtered_answers[] = $answer;
        }

        $this->answers = $filtered_answers;

        if ( count( $answers ) > 0 ) {
            $query = sprintf(
                'INSERT INTO `%1$sasq_answers` (answer_id,question_id,quiz_id,image_id,answer_title,answer_correct,answer_order,answer_guid) VALUES %2$s',
                $db->prefix,
                join( ',', $answers )
            );
            $query_result = $db->query( $query );

            $result = ( false !== $query_result );
        }

        return $result;
    }

    public function validate() {
        return true;
    }

    public function is_empty() {
        if ( ( strlen( $this->question_title ) == 0 && empty( $this->image_id ) ) || count( $this->answers ) == 0 ) {
            return true;
        }

        $is_empty = true;
        foreach ( $this->answers as $answer ) {
            if ( ! $this->is_empty_answer( $answer ) ) {
                $is_empty = false;
                break;
            }
        }

        return $is_empty;
    }

    protected function is_empty_answer( $answer ) {
        return strlen( $answer->answer_title ) == 0 && empty( $answer->image_id );
    }
}
