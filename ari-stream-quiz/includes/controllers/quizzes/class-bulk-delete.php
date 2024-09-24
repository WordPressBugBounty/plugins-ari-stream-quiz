<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Bulk_Delete extends Controller {
    public function execute() {
        check_admin_referer( 'asq-quiz' );

        $result = false;
        $model = $this->model();

        if ( Request::exists( 'quiz_id' ) ) {
            $quiz_id = Request::get_var( 'quiz_id', array() );
            if ( is_array( $quiz_id ) && count( $quiz_id ) > 0 ) {
				$quiz_id = Helper::filter_edit_quizzes( $quiz_id );

                $result = $model->delete( $quiz_id );
            }
        }

        if ( $result ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'The quizzes deleted successfully', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_SUCCESS,
                    )
                )
            );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'The quizzes can not be deleted', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_WARNING,
                    )
                )
            );
        }
    }
}
