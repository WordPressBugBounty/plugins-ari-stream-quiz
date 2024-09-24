<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Wordpress\Helper as WP_Helper;
use Ari\Wordpress\Kses_Helper as WP_Kses_Helper;
use Ari\Entities\Entity;

class Quiz extends Entity {
    public $quiz_id;

    public $quiz_title = '';

    public $quiz_title_filtered = '';

    public $quiz_description = '';

    public $quiz_image_id = 0;

    public $quiz_image = array();

    public $quiz_type = '';

    public $shuffle_answers = 0;

    public $random_questions = 0;

    public $random_question_count = 0;

    public $use_paging = 0;

    public $questions_per_page = 0;

    public $start_immediately = 0;

    public $question_count = 0;

    public $theme = '';

    public $collect_data = 0;

    public $collect_email = 1;

    public $collect_name = 0;

    public $collect_data_optional = 0;

    public $author_id = 0;

    public $created = '0000-00-00 00:00:00';

    public $modified = '0000-00-00 00:00:00';

    public $post_id = 0;

    public $quiz_meta = '';

    public $questions = array();

    protected $bool_fields = array(
        'random_questions',

        'shuffle_answers',

        'start_immediately',

        'use_paging',

        'collect_data',

        'collect_data_optional',

        'collect_name',

        'collect_email',
    );

    protected $meta_fields = array(
        'mailchimp' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'mailerlite' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'aweber' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'zapier' => array(
            'enabled' => false,

            'webhook_url' => '',
        ),

        'share_to_see' => false,

        'show_share_buttons' => false,

        'paging_nav_button' => false,

        'show_results' => '',

        'shortcode' => false,
    );

    protected $fields_to_filter = array(
        'quiz_title',

        'quiz_description',
    );

    protected $json_fields = array();

    protected $image_size = null;

    public function __construct( $db ) {
        parent::__construct( 'asq_quizzes', 'quiz_id', $db );

        $this->quiz_meta = json_decode( json_encode( $this->meta_fields ) );
    }

    public function bind( $data, $ignore = array() ) {
        if ( ! $this->is_new() ) {
            $ignore[] = 'quiz_type';
        }

        foreach ( $this->json_fields as $json_field ) {
            if ( ! empty( $data[ $json_field ] ) && is_string( $data[ $json_field ] ) ) {
                $data[ $json_field ] = json_decode( $data[ $json_field ], false );
            }
        }

        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $is_new = $this->is_new();
        $current_time_db_gmt = current_time( 'mysql', 1 );

        if ( $is_new ) {
            $this->author_id = get_current_user_id();
            $this->created = $current_time_db_gmt;
        } else {
            $this->modified = $current_time_db_gmt;
        }

        foreach ( $this->fields_to_filter as $field_to_filter ) {
            $this->$field_to_filter = WP_Kses_Helper::clean_by_cap( $this->$field_to_filter );
        }

        $this->quiz_title_filtered = wp_strip_all_tags( $this->quiz_title );
        $this->question_count = count( $this->questions );

        $quiz_meta = $this->quiz_meta;
        if ( ! is_string( $this->quiz_meta ) ) {
            $this->quiz_meta = json_encode( $this->quiz_meta );
        }

        $result = parent::store( $force_insert );

        $this->quiz_meta = $quiz_meta;

        if ( $result ) {
            $update_data = array();

            if ( 0 == $this->post_id ) {
                $post_id = wp_insert_post(
                    array(
                        'post_title' => $this->quiz_id,

                        'post_type' => ARISTREAMQUIZ_POST_TYPE,

                        'post_status' => 'publish',

                        'meta_input' => array(
                            'quiz_id' => $this->quiz_id,

                            'quiz_title' => $this->quiz_title,
                        ),
                    )
                );

                if ( $post_id ) {
                    $this->post_id = $post_id;

                    $this->db->update(
                        $this->db_tbl,
                        array(
                            'post_id' => $post_id,
                        ),
                        array(
                            'quiz_id' => $this->quiz_id,
                        )
                    );
                    $update_data['post_id'] = $post_id;

                    global $wp_version;

                    if ( $wp_version && version_compare( $wp_version, '4.4.0', '<' ) ) {
                        add_post_meta( $post_id, 'quiz_id', $this->quiz_id, true );
                        add_post_meta( $post_id, 'quiz_title', $this->quiz_title, true );
                    }
                }
            } else {
                update_post_meta( $this->post_id, 'quiz_title', $this->quiz_title );
            }
        }

        return $result;
    }

    public function copy() {
        if ( $this->is_new() ) {
            return false;
        }

        $quiz_class = get_class( $this );

        $quiz_copy = new $quiz_class( $this->db );

        if ( ! $quiz_copy->bind(
            $this->to_array(
                array(
					'quiz_id',

					'post_id',

					'author_id',

					'created',

					'modified',
				)
            )
        )
        ) {
            return false;
        }

        $quiz_copy->quiz_title = $this->get_unique_name();

        $this->prepare_copy( $quiz_copy );
        if ( ! $quiz_copy->store() ) {
            return false;
        }

        return $quiz_copy;
    }

    public function load( $keys, $reset = true ) {
        $result = parent::load( $keys, $reset );

        if ( ! $result ) {
            return $result;
        }

        if ( $this->quiz_meta ) {
            $quiz_meta = $this->meta_fields;
            $db_quiz_meta = json_decode( $this->quiz_meta, true );

            if ( is_array( $db_quiz_meta ) ) {
                $quiz_meta = array_replace_recursive( $quiz_meta, $db_quiz_meta );
            }

            $this->quiz_meta = json_decode( json_encode( $quiz_meta ) );
        } else {
            $this->quiz_meta = new \stdClass();
        }

        $images = $this->get_images();
        if ( $this->quiz_image_id > 0 ) {
            $this->quiz_image = $this->get_image( $this->quiz_image_id, $images );
        }

        $this->populate_entity( $images );

        return $result;
    }

    protected function prepare_copy( $quiz_copy ) {
    }

    public function collect_data() {
        if ( ! $this->collect_data ) {
            return false;
        }

        if ( ! $this->collect_name && ! $this->collect_email ) {
            return false;
        }

        return true;
    }

    public function need_to_process_user_data() {
        return (bool) $this->quiz_meta->mailchimp->enabled ||
            (bool) $this->quiz_meta->aweber->enabled ||
            (bool) $this->quiz_meta->zapier->enabled ||
            (bool) $this->quiz_meta->mailerlite->enabled;
    }

    public function get_unique_name( $name = null, $title_db_field = 'quiz_title' ) {
        return parent::get_unique_name( $name, $title_db_field );
    }

    public function validate() {
        if ( empty( $this->quiz_title ) ) {
            return false;
        }

        return true;
    }

    protected function populate_entity( $images ) {
    }

    protected function get_images() {
        return array();
    }

    protected function get_image( $image_id, $images, $default_image = array() ) {
        if ( 0 == $image_id || ! isset( $images[ $image_id ] ) ) {
            return $default_image;
        }

        $img = $images[ $image_id ];

        $image = new \stdClass();
        $image->url = $img->url;
        $image->description = $img->description;
        $image->width = $img->width;
        $image->height = $img->height;

        return $image;
    }

    public function set_image_size( $image_size ) {
        $this->image_size = $image_size;
    }
}
