<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model;
use Ari\Utils\Request;
use Ari\Utils\Array_Helper;
use Ari_Stream_Quiz\Helpers\Quizzes_Screen;
use Ari_Stream_Quiz\Models\Quiz as Quiz_Model;

class Quizzes extends Model {
    protected $sort_columns = array(
        'quiz_title',

        'created',

        'modified',
    );

    protected function populate_state() {
        $filter = array(
            'search' => '',

            'order_by' => '',

            'order_dir' => 'ASC',

            'page_size' => 10,

            'page_num' => 0,
        );

        $user_filter = null;
        if ( Request::exists( 'filter' ) ) {
            $user_filter = Request::get_var( 'filter' );

            if ( ! empty( $user_filter ) ) {
                $user_filter = json_decode( base64_decode( $user_filter ), true );
            } else {
				$user_filter = null;
            }
        }

        if ( is_array( $user_filter ) ) {
            foreach ( $user_filter as $filter_key => $filter_val ) {
                if ( isset( $filter[ $filter_key ] ) ) {
                    $filter[ $filter_key ] = $filter_val;
                }
            }
        }

        $screen_options = Quizzes_Screen::get_options();
        $filter['page_size'] = $screen_options['per_page'];

        $this->state['filter'] = $filter;
    }

    public function data() {
        $filter = $this->get_state( 'filter' );

        $items = $this->items( $filter );
        $items_count = $this->items_count( $filter );

        $data = array(
            'count' => $items_count,

            'list' => $items,

            'filter' => $filter,

            'filter_encoded' => $this->encoded_filter_state(),
        );

        return $data;
    }

    public function items( $filter = null ) {
        if ( is_null( $filter ) ) {
            $filter = $this->get_state( 'filter' );
        }

        $query = sprintf(
            'SELECT Q.quiz_id,Q.quiz_title,Q.quiz_title_filtered,Q.quiz_type,Q.question_count,Q.created,Q.modified,Q.post_id,Q.author_id,U.user_nicename AS author FROM `%1$sasq_quizzes` Q LEFT JOIN `%2$susers` U ON Q.author_id = U.ID',
            $this->db->prefix,
            $this->db->base_prefix
        );

        $query = $this->prepare_query( $query, $filter );

        $items = $this->db->get_results( $query, OBJECT );

        return $items;
    }

    public function items_count( $filter = null ) {
        if ( is_null( $filter ) ) {
            $filter = $this->get_state( 'filter' );
        }

        $query = sprintf(
            'SELECT COUNT(*) FROM `%1$sasq_quizzes` Q',
            $this->db->prefix
        );

        $query = $this->prepare_query( $query, $filter, false );

        $count = $this->db->get_var( $query );

        return $count;
    }

    protected function prepare_query( $query, $filter, $paging = true ) {
        $db = $this->db;

        $where = array();

        if ( ! empty( $filter['search'] ) ) {
            $where[] = $db->prepare( 'quiz_title_filtered LIKE CONCAT("%%",%s,"%%")', $filter['search'] );
        }

        if ( count( $where ) > 0 ) {
            $query .= ' WHERE ' . join( ' AND ', $where );
        }

        if ( $paging ) {
            if ( $filter['order_by'] && in_array( $filter['order_by'], $this->sort_columns ) ) {
                $order_by = $filter['order_by'];
                $order_dir = 'DESC' == $filter['order_dir'] ? 'DESC' : 'ASC';

                $query .= sprintf(
                    ' ORDER BY %s %s',
                    $order_by,
                    $order_dir
                );
            }

            if ( $filter['page_size'] > 0 && $filter['page_num'] >= 0 ) {
                $page_num = $filter['page_num'];
                $page_size = $filter['page_size'];

                $offset = $page_num * $page_size;

                $query .= sprintf(
                    ' LIMIT %d,%d',
                    $offset,
                    $page_size
                );
            }
        }

        return $query;
    }

    public function encoded_filter_state() {
        $filter = $this->get_state( 'filter' );

        return $filter ? base64_encode( json_encode( $filter ) ) : '';
    }

    public function copy( $id_list ) {
        if ( ! is_array( $id_list ) ) {
            $id_list = array( $id_list );
        }

        if ( count( $id_list ) == 0 ) {
            return false;
        }

        $id_list = array_map(
            function ( $v ) {
                return intval( $v, 10 );
            },
            $id_list
        );

        $quiz_model = new Quiz_Model(
            array(
                'class_prefix' => $this->options->class_prefix,
            )
        );

        $result = null;
        foreach ( $id_list as $id ) {
            $quiz = $quiz_model->get_quiz( $id );
            if ( empty( $quiz ) || 0 == $quiz->quiz_id ) {
                $result = false;
                continue;
            }

            $quiz_copy = $quiz->copy();

            if ( empty( $quiz_copy ) ) {
                $result = false;
                continue;
            }

            if ( is_null( $result ) ) {
                $result = true;
            }
        }

        return ! ! $result;
    }

    public function delete( $id_list ) {
        if ( ! is_array( $id_list ) ) {
            $id_list = array( $id_list );
        }

        if ( count( $id_list ) == 0 ) {
            return false;
        }

        $id_list = array_map(
            function ( $v ) {
                return intval( $v, 10 );
            },
            $id_list
        );

        $query = sprintf(
            'DELETE Q,QU,A,R,PST,PM FROM
              `%1$sasq_quizzes` Q LEFT JOIN `%1$sasq_questions` QU
                ON Q.quiz_id = QU.quiz_id
              LEFT JOIN `%1$sasq_answers` A
                ON QU.question_id = A.question_id
              LEFT JOIN `%1$sasq_result_templates` R
                ON R.quiz_id = Q.quiz_id
              LEFT JOIN `%1$sposts` PST
                ON PST.ID = Q.post_id
              LEFT JOIN `%1$spostmeta` PM
                ON PM.post_id = PST.ID
            WHERE Q.quiz_id IN (%2$s)',
            $this->db->prefix,
            join( ',', $id_list )
        );

        $result = $this->db->query( $query );

        return ( false !== $result );
    }

    public function get_quizzes_author_id( $id_list ) {
        $id_list = Array_Helper::to_int( $id_list, 1 );

        if ( count( $id_list ) == 0 ) {
            return array();
        }

        $res = $this->db->get_results(
            sprintf(
                'SELECT quiz_id,author_id FROM `%1$sasq_quizzes` WHERE quiz_id IN (%2$s)',
                $this->db->prefix,
                join( ',', $id_list )
            ),
            OBJECT_K
        );

        return $res;
    }

    public function get_quiz_author_id( $id ) {
        $quizzes_author_id = $this->get_quizzes_author_id( $id );

        return isset( $quizzes_author_id[ $id ] ) ? $quizzes_author_id[ $id ]->author_id : 0;
    }
}
