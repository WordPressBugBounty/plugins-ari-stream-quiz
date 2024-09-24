<?php
defined( 'ABSPATH' ) || die( 'Access forbidden!' );

if ( ! function_exists( 'ari_stream_quiz_init' ) ) {
    function ari_stream_quiz_init() {
        if ( defined( 'ARISTREAMQUIZ_INITED' ) ) {
            return;
        }

        define( 'ARISTREAMQUIZ_INITED', true );

        require_once ARISTREAMQUIZ_PATH . 'includes/defines.php';
        require_once ARISTREAMQUIZ_PATH . 'libraries/arisoft/loader.php';

        Ari_Loader::register_prefix( 'Ari_Stream_Quiz', ARISTREAMQUIZ_PATH . 'includes' );
        Ari_Loader::register_prefix( 'Ari_Stream_Quiz_Themes', ARISTREAMQUIZ_PATH . 'themes' );

        $plugin = new \Ari_Stream_Quiz\Plugin(
            array(
                'class_prefix' => 'Ari_Stream_Quiz',

                'version' => ARISTREAMQUIZ_VERSION,

                'path' => ARISTREAMQUIZ_PATH,

                'url' => ARISTREAMQUIZ_URL,

                'assets_url' => ARISTREAMQUIZ_ASSETS_URL,

                'view_path' => ARISTREAMQUIZ_PATH . 'includes/views/',

                'main_file' => __FILE__,

                'page_prefix' => 'ari-stream-quiz',
            )
        );
        $plugin->init();
    }
}
