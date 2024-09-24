<?php
if ( ! class_exists( 'Ari_Loader' ) ) {
	require_once __DIR__ . '/class-ari-loader.php';
}

Ari_Loader::register_prefix( 'Ari', __DIR__ . '/core' );
