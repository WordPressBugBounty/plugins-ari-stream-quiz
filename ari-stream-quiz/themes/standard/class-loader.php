<?php
namespace Ari_Stream_Quiz_Themes\Standard;

use Ari_Stream_Quiz_Themes\Loader as Loader_Base;

class Loader extends Loader_Base {
    protected $name = 'standard';

    public function init() {
        wp_enqueue_style( 'ari-quiz-standard-theme-font', 'https://fonts.googleapis.com/css?family=Montserrat' );
		wp_enqueue_style( 'ari-quiz-theme' );
    }
}
