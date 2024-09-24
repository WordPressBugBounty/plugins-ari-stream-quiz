<?php
namespace Ari_Stream_Quiz\Helpers;

class Quizzes_Screen {
    public static function register() {
        $args = array(
            'label' => __( 'Number of items per page', 'ari-stream-quiz' ),
            'default' => 25,
            'option' => 'aristreamquiz_quizzes_per_page',
        );

        add_screen_option( 'per_page', $args );

        $screen = get_current_screen();

        $screen->add_help_tab(
            array(
                'id' => 'asq_quizzes_help_tab',
                'title' => __( 'Help', 'ari-stream-quiz' ),
                'content' => sprintf(
                    /* translators: %s: link to user guide */
                    '<p>' . _x( 'User guide is available <a href="%s" target="_blank">here</a>.', '%s = link to user guide', 'ari-stream-quiz' ) . '</p>',
                    'http://ari-soft.com/docs/wordpress/ari-stream-quiz-free/v1/en/index.html'
                ),
            )
        );
    }

    public static function get_options() {
        $options = array();

        $user_id = get_current_user_id();
        $screen = get_current_screen();

        $per_page_option = $screen->get_option( 'per_page', 'option' );
        $per_page = get_user_meta( $user_id, $per_page_option, true );
        if ( empty( $per_page ) || $per_page < 1 ) {
            $per_page = $screen->get_option( 'per_page', 'default' );
        }

        $options['per_page'] = $per_page;

        return $options;
    }

    public static function set_options( $status, $option, $value ) {
        if ( 'aristreamquiz_quizzes_per_page' == $option ) {
            return $value;
        }

        return $status;
    }
}
