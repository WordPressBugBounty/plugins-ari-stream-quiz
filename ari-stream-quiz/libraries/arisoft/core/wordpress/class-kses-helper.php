<?php
namespace Ari\Wordpress;

final class Kses_Helper {
    public static function clean_by_cap( $content ) {
        if ( current_user_can( 'unfiltered_html' ) ) {
            return $content;
        }

        return wp_kses( $content, array() );
    }
}
