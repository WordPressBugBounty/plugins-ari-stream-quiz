<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Save extends Controller {
    public function execute() {
        $data = stripslashes_deep( Request::get_var( 'entity' ) );

        $quiz_id = ! empty( $data['quiz_id'] ) ? intval( $data['quiz_id'], 10 ) : 0;

        check_admin_referer( 'asq-edit-quiz_' . $quiz_id );

        if ( $quiz_id > 0 && ! Helper::can_edit_quiz( $quiz_id ) ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',
                    )
                )
            );
        }

        if ( ! isset( $data['quiz_meta'] ) ) {
            $data['quiz_meta'] = array();
        }

        $model = $this->model();
        $entity = $model->save( $data );
        $is_valid = ! empty( $entity );

        if ( $is_valid ) {
            $this->saved_successfully( $entity );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quiz',

                        'action' => 'add',

                        'msg' => __( 'The quiz is not saved. Probably data are corrupted or a database connection is broken.', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_ERROR,
                    )
                )
            );
        }
    }

    protected function saved_successfully( $entity, $url_params = array() ) {
        $default_ulr_params = array(
            'page' => 'ari-stream-quiz-quizzes',

            'msg' => __( 'The quiz is saved successfully', 'ari-stream-quiz' ),

            'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_SUCCESS,
        );
        $default_url_params = array_replace( $default_ulr_params, $url_params );
        $removed_params = array( 'id' );
        if ( ! array_key_exists( 'activeTab', $url_params ) ) {
            $removed_params[] = 'activeTab';
        }

        Response::redirect(
            Helper::build_url(
                $default_url_params,
                $removed_params
            )
        );
    }
}
