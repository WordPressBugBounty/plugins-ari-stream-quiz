<?php
namespace Ari_Stream_Quiz\Views\Quizzes;

use Ari_Stream_Quiz\Views\Base;

class Html extends Base {
    public $preview_post_id = 0;

    public function display( $tmpl = null ) {
        $this->set_title( __( 'Quizzes', 'ari-stream-quiz' ) );

        wp_enqueue_script( 'ari-clipboard' );

        wp_enqueue_script( 'ari-page-quizzes', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/quizzes.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        parent::display( $tmpl );
    }

    protected function get_app_options() {
        $app_options = array(
            'actionEl' => '#ctrl_action',

            'messages' => array(
                'deleteConfirm' => __( 'Do you want to delete the selected quiz?', 'ari-stream-quiz' ),

                'copyConfirm' => __( 'Create a copy of the selected quiz?', 'ari-stream-quiz' ),

                'bulkDeleteConfirm' => __( 'Do you want to delete the selected quizzes?', 'ari-stream-quiz' ),

                'bulkCopyConfirm' => __( 'Create copies of the selected quizzes?', 'ari-stream-quiz' ),

                'selectQuizzesWarning' => __( 'Select at least one quiz please.', 'ari-stream-quiz' ),

                'shortcodeCopied' => __( 'Copied', 'ari-stream-quiz' ),

                'shortcodeCopyFailed' => __( 'Press Ctrl+C to copy', 'ari-stream-quiz' ),
            ),

            'preview' => $this->preview_post_id > 0 ? get_permalink( $this->preview_post_id ) : null,
        );

        return $app_options;
    }
}
