<?php
namespace Ari\Utils;

class Sort_Utils {
    private $key;
    private $dir;
    private $cmp;

    public function __construct( $key, $dir = 'asc', $cmp = 'string' ) {
        $this->key = $key;
        $this->dir = strtolower( $dir );
        $this->cmp = strtolower( $cmp );
    }

    public function sort( $a, $b ) {
        $key = $this->key;
        $a_val = is_array( $a ) ? $a[ $key ] : $a->$key;
        $b_val = is_array( $b ) ? $b[ $key ] : $b->$key;

        $res = 0;
        if ( 'natural' == $this->cmp ) {
            $res = strnatcmp( $a_val, $b_val );
        } else {
			$res = strcmp( $a_val, $b_val );
        }

        return 'asc' == $this->dir ? $res : -$res;
    }
}
