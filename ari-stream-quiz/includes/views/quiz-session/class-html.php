<?php
namespace Ari_Stream_Quiz\Views\Quiz_Session;

use Ari_Stream_Quiz\Views\Site_Base;
use Ari_Stream_Quiz\Helpers\Helper;
use Ari_Stream_Quiz\Helpers\Settings;
use Ari\Wordpress\Helper as WP_Helper;

class Html extends Site_Base {
    protected static $js_l10n_loaded = false;

    public $quiz_data;

    public $share_buttons;

    public $need_to_load_facebook_sdk;

    public function display( $tmpl = null ) {
        $data = $this->get_data();
        if ( is_null( $tmpl ) || 'default' == $tmpl ) {
            if ( empty( $data['quiz'] ) ) {
                $tmpl = 'error';
            }
        }

        if ( 'error' == $tmpl ) {
            parent::display( $tmpl );
            return;
        }

        $quiz = $data['quiz'];
        $this->quiz_data = $this->model->prepare_quiz_data( $quiz );
        $this->need_to_load_facebook_sdk = $this->need_to_load_facebook_sdk( $quiz );
        $id = $this->id();
        $options = array(
            'prefix' => $id,

            'data' => $this->prepare_client_data( $this->quiz_data ),

            'ajaxUrl' => admin_url( 'admin-ajax.php?action=ari_stream_quiz' ),

            'nonce' => wp_create_nonce( 'asq-ajax-action' ),

            'smartScroll' => (bool) Settings::get_option( 'smart_scroll' ),

            'warningOnExit' => (bool) Settings::get_option( 'warning_on_exit' ),

            'lazyLoad' => (bool) Settings::get_option( 'lazy_load' ),

            'scroll' => array(
                'duration' => Settings::get_option( 'scroll_duration' ),

                'options' => array(
                    'offset' => Settings::get_option( 'scroll_offset' ),
                ),
            ),

            'messages' => array(
                'correct' => __( 'Correct', 'ari-stream-quiz' ),

                'wrong' => __( 'Wrong', 'ari-stream-quiz' ),
            ),
        );

        $js_l10n = array(
            'warningOnExit' => __( 'The quiz is not completed, do you want to leave the page?', 'ari-stream-quiz' ),
        );

		$inline_scripts = $data['inline_scripts'];

		if ( ! $inline_scripts ) {
        	wp_enqueue_script( 'ari-quiz' );

			wp_localize_script( 'ari-quiz', 'ARI_STREAM_QUIZ_' . $id, $options );

			if ( ! self::$js_l10n_loaded ) {
				wp_localize_script( 'ari-quiz', 'ARI_STREAM_QUIZ_L10N', $js_l10n );

				self::$js_l10n_loaded = true;
			}
		} else {
            wp_enqueue_script( 'jquery' );

            $script_vars = array();

            if ( ! self::$js_l10n_loaded ) {
                $script_vars['ARI_STREAM_QUIZ_L10N'] = $js_l10n;
            }

            $script_vars[ 'ARI_STREAM_QUIZ_' . $id ] = $options;
            $this->script_vars = $script_vars;
		}

        $this->share_buttons = Settings::get_option( 'share_buttons' );

        parent::display( $tmpl );
    }

    public function get_theme() {
        if ( ! is_null( $this->theme ) ) {
            return $this->theme;
        }

        $data = $this->get_data();
        $quiz = $data['quiz'];

        if ( empty( $quiz->theme ) ) {
            return parent::get_theme();
        }

        $theme = Helper::resolve_theme_name( $quiz->theme );
        $theme_class_name = \Ari_Loader::prepare_name( $theme );
        $theme_class = '\\Ari_Stream_Quiz_Themes\\' . $theme_class_name . '\\Loader';

        if ( ! class_exists( $theme_class ) ) {
            $theme_class = '\\Ari_Stream_Quiz_Themes\\Generic_Loader';
            $this->theme = new $theme_class( $theme );
        } else {
            $this->theme = new $theme_class();
        }

        return $this->theme;
    }

    protected function need_to_load_facebook_sdk( $quiz ) {
        if ( ! Settings::get_option( 'facebook_load_sdk' ) ) {
            return false;
        }

        $facebook_app_id = Settings::get_option( 'facebook_app_id' );
        if ( empty( $facebook_app_id ) ) {
            return false;
        }

        return $this->is_facebook_integration_required( $quiz );
    }

    protected function is_facebook_integration_required( $quiz ) {
        if ( $quiz->quiz_meta->show_share_buttons ) {
            $share_buttons = Settings::get_option( 'share_buttons' );

            if ( in_array( 'facebook', $share_buttons ) ) {
                return true;
            }
        }

        return false;
    }

    protected function prepare_client_data( $quiz_data ) {
        $model_data = $this->get_data();

        $quiz = $model_data['quiz'];

        $facebook_integration = $this->is_facebook_integration_required( $quiz );
        $facebook_app_id = $facebook_integration ? Settings::get_option( 'facebook_app_id' ) : '';

        $data = array(
            'quizId' => $quiz->quiz_id,

            'quizType' => $quiz->quiz_type,

            'startImmediately' => $quiz->start_immediately,

            'collectData' => $quiz->collect_data(),

            'processUserData' => $quiz->need_to_process_user_data(),

            'collectDataOptional' => $quiz->collect_data_optional,

            'collectName' => $quiz->collect_name,

            'collectEmail' => $quiz->collect_email,

            'questionCount' => $quiz_data->question_count,

            'pageCount' => 1,

            'pages' => array(),

            'share' => array(
                'url' => get_permalink(),

                'title' => $quiz->quiz_title,

                'description' => $quiz->quiz_description,

                'image' => $quiz->quiz_image_id > 0 ? $quiz->quiz_image->url : null,
            ),

            'facebook' => array(
                'enabled' => $facebook_integration,

                'settings' => array(
                    'appId' => $facebook_app_id,
                ),
            ),

            'lockoutAnswers' => (bool) Settings::get_option( 'lockout_answers', true ),
        );

        foreach ( $quiz_data->pages as $page ) {
            $current_page = array(
                'questions' => array(),
            );

            foreach ( $page->questions as $question ) {
                $current_question = array(
                    'answers' => array(),
                );

                foreach ( $question->answers as $answer ) {
                    $current_question['answers'][ $answer->answer_id ] = array(
                        'correct' => $answer->answer_correct,
                    );
                }

                $current_page['questions'][ $question->question_id ] = $current_question;
            }

            $data['pages'][] = $current_page;
        }

        $result_templates = array();

        $use_shortcode = $quiz->quiz_meta->shortcode;
        foreach ( $quiz->result_templates as $result_template ) {
            $result_templates[] = array(
                'title' => $result_template->template_title,

                'content' => $use_shortcode ? WP_Helper::do_shortcode( $result_template->template_content ) : $result_template->template_content,

                'image_id' => $result_template->image_id,

                'image' => $result_template->image_id > 0 ? $result_template->image : array(),

                'end_point' => $result_template->end_point,
            );
        }

        $data['resultTemplates'] = $result_templates;
        $data['showResults'] = Settings::get_option( 'show_results' );

        return base64_encode( json_encode( $data, JSON_NUMERIC_CHECK ) );
    }
}
