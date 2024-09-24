<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller;
use Ari\Utils\Response;
use Ari\Utils\Request;
use Ari_Stream_Quiz\Helpers\Helper;

class Search extends Controller {
    public function execute() {
        $model = $this->model();

        $params_mapping = array(
            'search' => 'search',

            'quiz_type' => 'quiz_type',
        );

        $search_data = null;
        if ( Request::exists( 'quiz_search' ) ) {
            $search_data = Request::get_var( 'quiz_search' );

            if ( is_array( $search_data ) ) {
                $search_data = stripslashes_deep( $search_data );
            } else {
				$search_data = array();
            }
        }

        if ( is_array( $search_data ) ) {
            $filter = $model->get_state( 'filter' );

            foreach ( $params_mapping as $request_key => $filter_key ) {
                if ( isset( $search_data[ $request_key ] ) ) {
                    $filter[ $filter_key ] = $search_data[ $request_key ];
                }
            }

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
