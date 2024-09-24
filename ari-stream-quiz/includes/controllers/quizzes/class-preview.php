<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Preview extends Controller {
    public function execute() {
        $post_link = '';
        if ( Request::exists( 'post_id' ) ) {
            $post_id = Request::get_var( 'post_id', 0, 'num' );
            if ( $post_id > 0 ) {
                $post_link = get_permalink( $post_id );
            }
        }

        if ( $post_link ) {
            Response::redirect( $post_link );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',

                        'msg' => __( 'Preview is not available for the selected quiz', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_WARNING,
                    )
                )
            );
        }
    }
}
