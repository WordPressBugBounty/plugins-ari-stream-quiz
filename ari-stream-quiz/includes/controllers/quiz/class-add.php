<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Display as Display_Controller;
use Ari\Utils\Request;
use Ari\Utils\Response;
use Ari_Stream_Quiz\Helpers\Helper;

class Add extends Display_Controller {
    public function execute() {
        unset( $_REQUEST['id'] );

        $type = Request::get_var( 'type' );
        if ( ! Helper::is_valid_quiz_type( $type ) ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',

                        'msg' => __( 'The selected quiz type is not supported.', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_WARNING,
                    ),
                    array(
                        'id',

                        'type',
                    )
                )
            );
        }

        parent::execute();
    }
}
