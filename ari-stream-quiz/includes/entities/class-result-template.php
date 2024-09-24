<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity;
use Ari\Utils\Utils;
use Ari\Wordpress\Kses_Helper as WP_Kses_Helper;

class Result_Template extends Entity {
    public $template_id;

    public $quiz_id;

    public $template_title;

    public $template_content;

    public $image_id = 0;

    public $image = array();

    public $end_point;

    protected $is_max_score = false;

    protected $fields_to_filter = array(
        'template_title',

        'template_content',
    );

    public function __construct( $db ) {
        parent::__construct( 'asq_result_templates', 'template_id', $db );
    }

    public function bind( $data, $ignore = array() ) {
        $result = parent::bind( $data, $ignore );

        if ( ! $result ) {
            return $result;
        }

        $end_point = Utils::get_value( $data, 'end_point' );
        if ( is_null( $end_point ) || '' === $end_point ) {
            $this->is_max_score = true;
            $this->end_point = ARISTREAMQUIZ_RESULTTEMPLATE_MAXSCORE;
        }

        return $result;
    }

    public function load( $keys, $reset = true ) {
        $result = parent::load( $keys, $reset );

        if ( ! $result ) {
            return $result;
        }

        if ( ARISTREAMQUIZ_RESULTTEMPLATE_MAXSCORE == $this->end_point ) {
            $this->is_max_score = true;
        }

        return $result;
    }

    public function is_max_score() {
        return $this->is_max_score;
    }

    public function validate() {
        return true;
    }

    public function is_empty() {
        if ( empty( $this->template_title ) && empty( $this->description ) && empty( $this->image_id ) && $this->is_max_score() ) {
            return true;
        }

        return false;
    }

    public function store( $force_insert = false ) {
        foreach ( $this->fields_to_filter as $field_to_filter ) {
            $this->$field_to_filter = WP_Kses_Helper::clean_by_cap( $this->$field_to_filter );
        }

        return parent::store( $force_insert );
    }
}
