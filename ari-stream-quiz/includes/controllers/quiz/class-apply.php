<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Utils\Request;

class Apply extends Save {
    protected function saved_successfully( $entity, $url_params = array() ) {
        $active_tab = Request::get_var( 'quizActiveTab' );

        $url_params = array(
            'page' => 'ari-stream-quiz-quiz',

            'action' => 'edit',

            'id' => $entity->quiz_id,

            'activeTab' => $active_tab,
        );

        parent::saved_successfully( $entity, $url_params );
    }
}
