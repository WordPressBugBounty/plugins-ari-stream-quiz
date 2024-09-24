<?php
namespace Ari_Stream_Quiz_Themes;

class Generic_Loader extends Loader {
    public function __construct( $name ) {
        $this->name = $name;
    }

    public function init() {
        $theme_css_file = ARISTREAMQUIZ_THEMES_PATH . $this->name . '/theme.css';

        if ( file_exists( $theme_css_file ) ) {
            wp_enqueue_style( 'ari-quiz-theme-' . $this->name, ARISTREAMQUIZ_THEMES_URL . $this->name . '/theme.css', array(), ARISTREAMQUIZ_VERSION );
        }
    }
}
