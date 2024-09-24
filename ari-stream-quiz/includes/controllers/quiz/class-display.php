<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari_Stream_Quiz\Helpers\Helper;

class Display extends Controller {
    public function execute() {
        Response::redirect(
            Helper::build_url(
                array(
                    'page' => 'ari-stream-quiz-quizzes',
                ),
                array(
                    'id',
                    'type',
                )
            )
        );
    }
}
