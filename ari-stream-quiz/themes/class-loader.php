<?php
namespace Ari_Stream_Quiz_Themes;

abstract class Loader {
    protected $name = null;

    public function init() {
    }

    public function get_views_path() {
        $folder = strtolower(
            str_replace( '_', '-', $this->name() )
        );

        return ARISTREAMQUIZ_PATH . 'themes/' . $folder . '/views/';
    }

    public function name() {
        if ( ! empty( $this->name ) ) {
            return $this->name;
        }

		$class = explode( '\\', get_class( $this ) );
        $this->name = strtolower( $class[ count( $class ) - 2 ] );

        return $this->name;
    }
}
