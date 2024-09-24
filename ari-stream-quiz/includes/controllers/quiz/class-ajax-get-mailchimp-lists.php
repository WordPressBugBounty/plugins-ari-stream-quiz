<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Ajax_Get_Mailchimp_Lists extends Ajax_Controller {
    protected function process_request() {
        if ( $this->options->nopriv || ! check_ajax_referer( 'asq-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) || ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_posts' ) ) ) {
            return null;
        }

        $reload = (bool) Request::get_var( 'reload' );

        $lists = Helper::get_mailchimp_lists( $reload );

        return $lists;
    }
}
