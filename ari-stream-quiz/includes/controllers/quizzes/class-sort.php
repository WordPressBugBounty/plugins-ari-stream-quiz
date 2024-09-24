<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Sort extends Controller {
    public function execute() {
        $model = $this->model();

        if ( Request::exists( 'quiz_sort' ) ) {
            $sort_data = Request::get_var( 'quiz_sort' );

            if ( is_array( $sort_data ) ) {
                $sort_data = stripslashes_deep( $sort_data );
            } else {
				$sort_data = array();
            }
        }

        if ( is_array( $sort_data ) ) {
            $filter = $model->get_state( 'filter' );

            $sort_column = ! empty( $sort_data['column'] ) ? $sort_data['column'] : '';
            $sort_dir = ! empty( $sort_data['dir'] ) ? $sort_data['dir'] : 'ASC';

            $filter['order_by'] = $sort_column;
            $filter['order_dir'] = $sort_dir;
            $filter['page_num'] = 0;

            $model->set_state( 'filter', $filter );
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
