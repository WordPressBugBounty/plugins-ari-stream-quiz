<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Display as DisplayController;
use Ari\Utils\Response;
use Ari_Stream_Quiz\Helpers\Helper;

class Edit extends DisplayController {
    public function execute() {
        $model = $this->model();
        $data = $model->data();

        if ( empty( $data['entity'] ) ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',
                    ),
                    array(
                        'id',
                    )
                )
            );
        } else if ( ! Helper::can_edit_other_quizzes() && get_current_user_id() != $data['entity']->author_id ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',
                    ),
                    array(
                        'id',
                    )
                )
            );
        }

        parent::execute();
    }
}
