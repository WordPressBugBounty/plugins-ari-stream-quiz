<?php
namespace Ari_Stream_Quiz\Views;

use Ari\Views\View;
use Ari_Stream_Quiz\Helpers\Helper;
use Ari_Stream_Quiz\Helpers\Settings;

class Site_Base extends View {
    protected static $is_custom_styles_loaded = false;

    protected $id = null;

    protected $theme = null;

    protected $theme_loaded = false;

    public function id() {
        if ( ! is_null( $this->id ) ) {
            return $this->id;
        }

        $this->id = uniqid( 'asq_', false );

        return $this->id;
    }

    public function display( $tmpl = null ) {
        wp_enqueue_script( 'ari-streamquiz-app' );

        $this->init_theme();
        $theme_name = $this->get_theme()->name();
        $id = $this->id();

        $custom_styles = Settings::get_option( 'custom_styles' );

        if ( $custom_styles && ! self::$is_custom_styles_loaded ) {
            printf(
                '<style>%1$s</style>',
                esc_html( $custom_styles )
            );

            self::$is_custom_styles_loaded = true;
        }

        echo '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( 'asq-theme asq-theme-' . $theme_name ) . '">';

        parent::display( $tmpl );

        echo '</div>';
    }

    public function get_theme() {
        if ( ! is_null( $this->theme ) ) {
            return $this->theme;
        }

        $default_theme = Settings::get_option( 'theme', 'standard' );
        $theme = Helper::resolve_theme_name( $default_theme );
        $theme_class_name = \Ari_Loader::prepare_name( $theme );
        $theme_class = '\\Ari_Stream_Quiz_Themes\\' . $theme_class_name . '\\Loader';

        if ( ! class_exists( $theme_class ) ) {
            $theme_class = '\\Ari_Stream_Quiz_Themes\\Generic_Loader';
            $this->theme = new $theme_class( $theme );
        } else {
            $this->theme = new $theme_class();
        }

        return $this->theme;
    }

    protected function init_theme() {
        if ( $this->theme_loaded ) {
            return;
        }

        $theme = $this->get_theme();
        $theme->init();
        $theme_views_path = $theme->get_views_path();

        if ( $theme_views_path ) {
            $theme_views_path .= $this->options->domain . '/';
            $this->add_path( $theme_views_path );
        }

        $this->theme_loaded = true;
    }
}
