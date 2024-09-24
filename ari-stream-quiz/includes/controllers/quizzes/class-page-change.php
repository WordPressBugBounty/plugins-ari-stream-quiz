<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Page_Change extends Controller {
    public function execute() {
        $model = $this->model();

        if ( Request::exists( 'quiz_page' ) ) {
            $page_num = Request::get_var( 'quiz_page', 0, 'num' );

            if ( $page_num >= 0 ) {
                $filter = $model->get_state( 'filter' );
                $filter['page_num'] = $page_num;
                $model->set_state( 'filter', $filter );
            }
        }

        Response::redirect(
            Helper::build_url(
                array(
                    'page' => 'ari-stream-quiz-quizzes',

                    'filter' => $model->encoded_filter_state(),
                )
            )
        );
    }
}
