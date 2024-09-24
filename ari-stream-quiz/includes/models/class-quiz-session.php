<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model;
use Ari_Stream_Quiz\Models\Quiz as Quiz_Model;
use Ari_Stream_Quiz\Helpers\Settings;

class Quiz_Session extends Model {
    public function data() {
        $quiz_model = new Quiz_Model(
            array(
                'class_prefix' => $this->options->class_prefix,

                'disable_state_load' => true,
            )
        );

        $id = $this->get_state( 'id' );
        $col = intval( $this->get_state( 'col', Settings::get_option( 'shortcode_quiz_column_count', 2 ) ), 10 );
        $quiz = $quiz_model->get_quiz( $id );

        if ( $col < 1 ) {
            $col = 1;
        }

        $inline_scripts = $this->get_state( 'inline_scripts', false );

        if ( '0' === $inline_scripts ) {
            $inline_scripts = false;
        } else {
			$inline_scripts = (bool) $inline_scripts;
        }

        $data = array(
            'id' => $this->get_state( 'id' ),

            'hide_title' => (bool) $this->get_state( 'hide_title', (bool) Settings::get_option( 'shortcode_quiz_hide_title', false ) ),

            'column_count' => $col,

            'quiz' => $quiz,

			'inline_scripts' => $inline_scripts,
        );

        return $data;
    }

    public function prepare_quiz_data( $quiz = null ) {
        if ( is_null( $quiz ) ) {
            $quiz = $this->data()['quiz'];

            if ( is_null( $quiz ) ) {
                return null;
            }
        }

        $data = new \stdClass();
        $data->pages = array();

        $shuffle_answers = (bool) $quiz->shuffle_answers;
        $random = (bool) $quiz->random_questions;
        $question_number = $quiz->random_question_count;
        $question_per_page = (bool) $quiz->use_paging ? $quiz->questions_per_page : 0;

        $questions = $quiz->questions;

        $question_count = count( $questions );
        if ( $random ) {
            shuffle( $questions );

            if ( $question_number > 0 && $question_count > $question_number ) {
                $questions = array_slice( $questions, 0, $question_number );
                $question_count = $question_number;
            }
        }

        if ( $shuffle_answers ) {
            foreach ( $questions as $question ) {
                shuffle( $question->answers );
            }
        }

        if ( $question_per_page > 0 ) {
            $page_count = ceil( $question_count / $question_per_page );

            for ( $i = 0; $i < $page_count; $i++ ) {
                $page = new \stdClass();
                $page->questions = array_slice( $questions, $question_per_page * $i, $question_per_page );

                $data->pages[] = $page;
            }
        } else {
            $page = new \stdClass();
            $page->questions = $questions;

            $data->pages[] = $page;
        }

        $data->question_count = $question_count;

        $data = apply_filters( 'asq_quiz_prepare_data', $data );

        return $data;
    }
}
