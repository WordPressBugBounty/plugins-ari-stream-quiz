<?php
namespace Ari\Utils;

class Options {
	public function __construct( $options = array() ) {
		foreach ( $options as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
			    $this->$key = $value;
            }
		}
	}
}
