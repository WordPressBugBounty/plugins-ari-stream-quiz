<?php
namespace Ari_Stream_Quiz_Themes\Buzzfeed;

use Ari_Stream_Quiz_Themes\Loader as Loader_Base;

class Loader extends Loader_Base {
    protected $name = 'buzzfeed';

    public function init() {
		$theme_css_file = ARISTREAMQUIZ_THEMES_URL . $this->name . '/css/theme.css';

		wp_enqueue_style( 'ari-quiz-theme-buzzfeed', $theme_css_file, array(), ARISTREAMQUIZ_VERSION );
    }
}
