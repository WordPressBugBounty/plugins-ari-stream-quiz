<?php
namespace Ari\Utils;

class Filter {
    public static function filter( $val, $filter = null ) {
        if ( empty( $filter ) || empty( $val ) ) {
            return $val;
        }

        $filter_method = 'filter_' . $filter;

        return self::$filter_method( $val );
    }

    public static function filter_cmd( $val ) {
        return preg_replace( '/[^A-Z0-9_\.-]/i', '', $val );
    }

    public static function filter_alphanum( $val ) {
        return preg_replace( '/[^A-Z0-9]/i', '', $val );
    }

    public static function filter_alpha( $val ) {
        return preg_replace( '/[^A-Z]/i', '', $val );
    }

    public static function filter_num( $val ) {
        return preg_replace( '/[^0-9]/i', '', $val );
    }
}
