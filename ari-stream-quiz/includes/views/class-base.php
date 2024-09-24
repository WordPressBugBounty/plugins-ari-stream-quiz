<?php
namespace Ari_Stream_Quiz\Views;

use Ari\Views\View;
use Ari\Utils\Request;

class Base extends View {
    protected $title = '';

    protected $main_class = '';

    public function display( $tmpl = null ) {
        wp_enqueue_script( 'ari-streamquiz-app' );
        wp_enqueue_script( 'ari-materialize' );
        wp_enqueue_style( 'ari-materialize-icons' );
        wp_enqueue_style( 'ari-streamquiz-materialize' );

        $container_class = 'material-wrap wrap' . ( $this->main_class ? ' ' . $this->main_class : '' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<div id="ari-stream-quiz-plugin" class="' . esc_attr( $container_class ) . '"><div class="material-app-loading" style="text-align:center;min-height:300px;padding-top:114px;">' . $this->get_loading_icon() . '</div><div class="material-app-pane" style="display:none">';

        $this->render_title();
        $this->render_message();

        parent::display( $tmpl );

        echo '</div></div>';
        $app_options = $this->get_app_options();

        $app_helper_options = array(
            'messages' => array(
                'yes' => __( 'Yes', 'ari-stream-quiz' ),

                'no' => __( 'No', 'ari-stream-quiz' ),

                'ok' => __( 'OK', 'ari-stream-quiz' ),

                'close' => __( 'Close', 'ari-stream-quiz' ),
            ),
        );

        printf(
            '<script>jQuery(function($) { AppHelper.options = $.extend(true, AppHelper.options, %2$s); AppHelper.createApp("ari-stream-quiz-plugin", %1$s); });</script>',
            json_encode( $app_options, JSON_NUMERIC_CHECK ),
            json_encode( $app_helper_options )
        );
    }

    public function set_title( $title ) {
        $this->title = $title;
    }

    protected function render_title() {
        if ( $this->title ) {
            printf(
                '<h5>%s</h5>',
                esc_html( $this->title )
            );
        }
    }

    protected function render_message() {
        if ( ! Request::exists( 'msg' ) ) {
            return;
        }

        $message_type = Request::get_var( 'msg_type', ARISTREAMQUIZ_MESSAGETYPE_NOTICE, 'alpha' );
        $message = esc_html( Request::get_var( 'msg' ) );

        printf(
            '<div class="notice notice-%2$s is-dismissible"><p>%1$s</p></div>',
            esc_html( $message ),
            esc_attr( $message_type )
        );
    }

    protected function get_app_options() {
        return null;
    }

    protected function get_loading_icon() {
        return '<svg width="72px" height="72px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-squares"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect x="15" y="15" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.0s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="40" y="15" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.125s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="65" y="15" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.25s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="15" y="40" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.875s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="65" y="40" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.375" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="15" y="65" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.75s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="40" y="65" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.625s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect><rect x="65" y="65" width="20" height="20" fill="#cec9c9" class="sq"><animate attributeName="fill" from="#cec9c9" to="#3c302e" repeatCount="indefinite" dur="1s" begin="0.5s" values="#3c302e;#3c302e;#cec9c9;#cec9c9" keyTimes="0;0.1;0.2;1"></animate></rect></svg>';
    }
}
