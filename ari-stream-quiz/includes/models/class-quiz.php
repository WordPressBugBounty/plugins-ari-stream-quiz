<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model;
use Ari\Utils\Request;

class Quiz extends Model {
    protected function populate_state() {
        if ( Request::exists( 'id' ) ) {
            $this->state['entity.id'] = Request::get_var( 'id', 0, 'num' );
        }

        if ( Request::exists( 'type' ) ) {
            $this->state['entity.type'] = Request::get_var( 'type' );
        }
    }

    public function data() {
        $id = $this->get_state( 'entity.id' );
        $entity = $this->entity_by_params( $id, $this->get_state( 'entity.type' ), 'medium' );

        $data = array(
            'entity' => $entity,
        );

        return $data;
    }

    public function get_quiz( $id, $image_size = null ) {
        $quiz_type = $this->db->get_var(
            sprintf(
                'SELECT quiz_type FROM `%1$sasq_quizzes` WHERE quiz_id = %2$d',
                $this->db->prefix,
                $id
            )
        );

        if ( is_null( $quiz_type ) ) {
            return null;
        }

        $entity = $this->entity_by_type( $quiz_type );
        $entity->set_image_size( $image_size );
        $entity->load( $id );

        return $entity;
    }

    protected function entity_by_params( $id, $quiz_type, $image_size = null ) {
        $entity = null;

        if ( ! empty( $id ) ) {
            $entity = $this->get_quiz( $id, $image_size );
        } else {
            $entity = $this->entity_by_type( $quiz_type );
        }

        return $entity;
    }

    protected function entity_by_type( $quiz_type ) {
        if ( empty( $quiz_type ) ) {
            return null;
        }

        $entity_name = 'Quiz_' . ucfirst( strtolower( $quiz_type ) );

        return $this->entity( $entity_name );
    }

    public function save( $data ) {
        $id = ! empty( $data['quiz_id'] ) ? intval( $data['quiz_id'], 10 ) : 0;
        $quiz_type = ! empty( $data['quiz_type'] ) ? $data['quiz_type'] : '';

        $quiz = $this->entity_by_params( $id, $quiz_type );

        if ( empty( $quiz ) ) {
            return false;
        }

        if ( $id > 0 ) {
            if ( ! $quiz->load( $id ) ) {
                return false;
            }
        }

        if ( ! $quiz->bind( $data ) ) {
            return false;
        }

        if ( ! $quiz->validate() ) {
            return false;
        }

        if ( ! $quiz->store() ) {
            return false;
        }

        do_action( 'asq_quiz_after_save', $quiz );

        return $quiz;
    }

    public function get_quiz_meta_tags( $quiz_id ) {
        $query = sprintf(
            'SELECT quiz_description,quiz_image_id FROM `%1$sasq_quizzes` Q WHERE quiz_id = %2$d',
            $this->db->prefix,
            $quiz_id
        );

        $result = $this->db->get_row( $query );

        return $result;
    }
}
