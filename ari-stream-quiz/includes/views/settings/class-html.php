<?php
namespace Ari_Stream_Quiz\Views\Settings;

use Ari_Stream_Quiz\Views\Base;

class Html extends Base {
    public function display( $tmpl = null ) {
        $this->set_title( __( 'ARI Stream Quiz - Settings', 'ari-stream-quiz' ) );

        wp_enqueue_script( 'ari-page-settings', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/settings.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        parent::display( $tmpl );
    }
}
