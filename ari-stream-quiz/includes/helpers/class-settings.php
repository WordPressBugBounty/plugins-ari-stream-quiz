<?php
namespace Ari_Stream_Quiz\Helpers;

define( 'ARISTREAMQUIZ_SETTINGS_GROUP', 'ari_stream_quiz' );
define( 'ARISTREAMQUIZ_SETTINGS_NAME', 'ari_stream_quiz_settings' );

define( 'ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE', 'ari-stream-quiz-settings-general' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_PAGE', 'ari-stream-quiz-settings-sharing' );
define( 'ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE', 'ari-stream-quiz-settings-advanced' );

define( 'ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION', 'ari_stream_quiz_general_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION', 'ari_stream_quiz_shortcode_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_SECTION', 'ari_stream_quiz_sharing_section' );
define( 'ARISTREAMQUIZ_SETTINGS_TRIVIAQUIZ_SECTION', 'ari_stream_quiz_triviaquiz_section' );
define( 'ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION', 'ari_stream_quiz_mailchimp_section' );
define( 'ARISTREAMQUIZ_SETTINGS_MAILERLITE_SECTION', 'ari_stream_quiz_mailerlite_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION', 'ari_stream_quiz_sharing_triviacontent_section' );
define( 'ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION', 'ari_stream_quiz_advanced_section' );

define( 'ARISTREAMQUIZ_SETTINGS_ARRAY_DELIMITER', ';' );

use Ari_Stream_Quiz\Helpers\Helper;

class Settings {
    private static $options = null;

    private static $default_settings = array(
        'theme' => ARISTREAMQUIZ_THEME_DEFAULT,

        'smart_scroll' => true,

        'scroll_duration' => 600,

        'scroll_offset' => 0,

        'custom_styles' => '',

        'show_results' => '', // empty, 'immediately', 'on_complete'

        'show_questions_oncomplete' => true,

        'share_trivia_title' => 'You got {{userScore}} out of {{maxScore}} correct',

        'share_trivia_facebook_title' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_facebook_content' => '{{content}}',

        'share_trivia_twitter_content' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_email_subject' => '{{title}}',

        'share_trivia_email_body' => '{{url}}',

        'warning_on_exit' => false,

        'mailchimp_apikey' => '',

        'lazy_load' => true,

        'facebook_app_id' => '',

        'facebook_load_sdk' => true,

        'share_buttons' => array( 'facebook', 'twitter' ),

        'clean_uninstall' => false,

        'shortcode_quiz_hide_title' => false,

        'shortcode_quiz_column_count' => 2,

        'lockout_answers' => true,

        'mailerlite_apikey' => '',

        'add_meta_tags' => true,

        'disable_script_optimization' => false,
    );

    public static function init() {
        register_setting(
            ARISTREAMQUIZ_SETTINGS_GROUP,
            ARISTREAMQUIZ_SETTINGS_NAME,
            array( __CLASS__, 'sanitize' )
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION,
            '', // Title
            array( __CLASS__, 'render_shortcode_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION,
            '', // Title
            array( __CLASS__, 'render_general_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION,
            '', // Title
            array( __CLASS__, 'render_mailchimp_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_MAILERLITE_SECTION,
            '', // Title
            array( __CLASS__, 'render_mailerlite_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION,
            '', // Title
            array( __CLASS__, 'render_sharing_section_info' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION,
            '', // Title
            array( __CLASS__, 'render_sharing_triviacontent_section_info' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION,
            '', // Title
            array( __CLASS__, 'render_advanced_section_info' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE
        );

        add_settings_field(
            'shortcode_quiz_hide_title',
            self::format_option_name(
                __( 'Hide title', 'ari-stream-quiz' ),
                __( 'If the parameter is enabled, title of the quizzes which are embedded via shortcode will be hidden. Can be changed directly into shortcode via hide_title shortcode parameter.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_shortcode_quiz_hide_title' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION
        );

        add_settings_field(
            'shortcode_quiz_column_count',
            self::format_option_name(
                __( 'Image answers per row', 'ari-stream-quiz' ),
                __( 'It is used to specify how many image-based answers will be shown per row. Can be changed directly into shortcode via col shortcode parameter.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_shortcode_quiz_col_count' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION
        );

        add_settings_field(
            'theme',
            self::format_option_name(
                __( 'Default theme', 'ari-stream-quiz' ),
                __( 'The selected theme will be used for all quizzes by default if it is not overridden in quiz settings.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_theme' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'smart_scroll',
            self::format_option_name(
                __( 'Smart scroll', 'ari-stream-quiz' ),
                __( 'If the parameter is enabled, the extension will automatically scroll to next element (question, quiz result and etc.) during quiz session.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_smart_scroll' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'scroll_duration',
            self::format_option_name(
                __( 'Scroll duration', 'ari-stream-quiz' ),
                __( 'The duration in milliseconds of scrolling animation.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_scroll_duration' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'scroll_offset',
            self::format_option_name(
                __( 'Scroll offset', 'ari-stream-quiz' ),
                __( 'The defined offset in pixels will be added to final top position, useful if template contains fixed elements. Possible to use negative values.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_scroll_offset' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'show_questions_oncomplete',
            self::format_option_name(
                __( 'Show questions at the end', 'ari-stream-quiz' ),
                __( 'If it is enabled, all questions will be shown on quiz final page otherwise questions will be hidden.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_show_questions_oncomplete' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'warning_on_exit',
            self::format_option_name(
                __( 'Warning on exit', 'ari-stream-quiz' ),
                __( 'Warning message will be shown if a user leaves non-completed quiz.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_warning_on_exit' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        // Trivia quiz parameters
        add_settings_field(
            'show_results',
            self::format_option_name(
                __( 'Show result per question', 'ari-stream-quiz' ),
                __( 'Specify should quiz takers see correct answers or not.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_triviaquiz_show_results' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'lockout_answers',
            self::format_option_name(
                __( 'Lockout single answers', 'ari-stream-quiz' ),
                __( 'If the parameter is activated, answers will be disabled when a user selected an answer otherwise users can change their answers.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_triviaquiz_lockout_answers' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        // MailChimp parameters
        add_settings_field(
            'mailchimp_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),
                __( 'API key is required for integration with MailChimp service. Login to your MailChimp account, generate API key, copy it and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mailchimp_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION
        );

        // MailerLite parameters
        add_settings_field(
            'mailerlite_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),
                __( 'API key is required for integration with MailerLite service. Login to your MailerLite account, generate API key, copy it and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mailerlite_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAILERLITE_SECTION
        );

        // Sharing parameters
        add_settings_field(
            'facebook_app_id',
            self::format_option_name(
                __( 'Facebook App ID', 'ari-stream-quiz' ),
                __( 'App ID is required to use "Facebook" share button. If App ID is not defined, it will not be possible to specify title, description and image for sharing content.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_share_facebook_app_id' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION
        );

        add_settings_field(
            'share_buttons',
            self::format_option_name(
                __( 'Share buttons', 'ari-stream-quiz' ),
                __( 'The selected share buttons will be shown on quiz final page.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_share_share_buttons' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION
        );

        // Sharing content - trivia quiz
        add_settings_field(
            'share_trivia_description',
            '',
            array( __CLASS__, 'render_share_triviaquiz_description' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_title',
            __( 'Title on result page', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_facebook_title',
            __( 'Title of Facebook post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_facebook_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_facebook_content',
            __( 'Content of Facebook post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_facebook_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_twitter_content',
            __( 'Content of Twitter post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_twitter_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_email_subject',
            __( 'Mail subject', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_email_subject' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_email_body',
            __( 'Mail body', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_email_body' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        // Advanced parameters
        add_settings_field(
            'clean_uninstall',
            self::format_option_name(
                __( 'Clean uninstall', 'ari-stream-quiz' ),
                __( 'If the parameter is enabled, plugin\'s settings and data will be removed when the plugin is uninstalled. Don\'t enable this option if want to upgrade the plugin and keep quizzes.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_clean_uninstall' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'disable_script_optimization',
            self::format_option_name(
                __( 'Disable script optimization', 'ari-stream-quiz' ),
                __( 'If the parameter is enabled, the plugin will try to avoid optimization of script loading by 3rd party plugins.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_disable_script_optimization' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'facebook_load_sdk',
            self::format_option_name(
                __( 'Load Facebook SDK', 'ari-stream-quiz' ),
                __( 'If template or another plugin also loads Facebook JS SDK, it is possible to disabled SDK loading by the plugin to avoid conflicts.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_facebook_load_sdk' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'add_meta_tags',
            self::format_option_name(
                __( 'Add meta tags', 'ari-stream-quiz' ),
                __( 'If the parameter is enabled, the plugin will add Open Graph and Twitter meta tags for current quiz.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_add_meta_tags' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'lazy_load',
            self::format_option_name(
                __( 'Images lazy loading', 'ari-stream-quiz' ),
                __( 'If the parameter is enabled, images in questions and answers will be loaded only when quiz is started (if "Start quiz" button is used) to increase page speed and a loading icon will be shown until images are loaded.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_lazy_load' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'custom_styles',
            self::format_option_name(
                __( 'Custom CSS styles', 'ari-stream-quiz' ),
                __( 'The defined CSS rules will be added on frontend pages with quizzes. Can be used to resolve style conflicts or for customization.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_custom_styles' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );
    }

    public static function options() {
        if ( ! is_null( self::$options ) ) {
            return self::$options;
        }

        self::$options = get_option( ARISTREAMQUIZ_SETTINGS_NAME );

        return self::$options;
    }

    public static function get_option( $name, $default = null ) {
        $options = self::options();

        $val = $default;

        if ( isset( $options[ $name ] ) ) {
            $val = $options[ $name ];
        } else if ( is_null( $default ) && isset( self::$default_settings[ $name ] ) ) {
            $val = self::$default_settings[ $name ];
        }

        return $val;
    }

    public static function format_option_name( $title, $tooltip = '' ) {
        $html = $title;

        if ( $tooltip ) {
            $html = sprintf(
                '<span class="tooltipped" data-position="top" data-tooltip="%2$s">%1$s</span>',
                $title,
                esc_attr( $tooltip )
            );
        }

        return $html;
    }

    public static function render_header( $message, $class = '' ) {
        printf(
            '<div class="%2$s">%1$s</div>',
            esc_html( $message ),
            esc_attr( 'section-header ' . $class )
        );
    }

    public static function render_general_section_info() {
        self::render_header( __( 'Contains global parameters for configuration quizzes look\'n\'feel.', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_shortcode_section_info() {
        self::render_header( __( 'Configure shortcode parameters', 'ari-stream-quiz' ) );
    }

    public static function render_mailchimp_section_info() {
        self::render_header( __( 'The parameters are used for integration with MailChimp service', 'ari-stream-quiz' ) );
    }

    public static function render_mailerlite_section_info() {
        self::render_header( __( 'The parameters are used for integration with MailerLite service', 'ari-stream-quiz' ) );
    }

    public static function render_sharing_section_info() {
        self::render_header( __( 'Contains parameters for configuration share buttons', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_sharing_triviacontent_section_info() {
        self::render_header( __( 'This parameters section is used to configure content for quiz final page.', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_share_triviaquiz_description() {
        echo wp_kses(
            sprintf(
                '<div class="settings-description">%s</div>',
                __( 'The following predefined variables are supported: <ul><li><b>{{userScore}}</b> contains number of correctly answered questions</li><li><b>{{userScorePercent}}</b> contains number of correctly answered questions in percent</li><li><b>{{maxScore}}</b> contains number of questions</li><li><b>{{title}}</b> contains title of result template</li><li><b>{{content}}</b> contains content of result template</li><li><b>{{quiz}}</b> contains quiz name</li><li><b>{{url}}</b> contains page URL</li></ul>', 'ari-stream-quiz' )
            ),
            array(
                'ul' => true,
                'li' => true,
                'b' => true,
            )
        );
    }

    public static function render_advanced_section_info() {
        self::render_header( __( 'This section contains advanced parameters for fine tuning of the plugin', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_shortcode_quiz_hide_title() {
        $val = self::get_option( 'shortcode_quiz_hide_title' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkShortcodeHideTitle" name="%1$s" value="1"%2$s /><label for="chkShortcodeHideTitle"> </label>',
            esc_attr( sprintf( '%1$s[shortcode_quiz_hide_title]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_shortcode_quiz_col_count() {
        $val = self::get_option( 'shortcode_quiz_column_count' );

        printf(
            '<input type="number" class="input-small center-align" id="tbxShortcodeColCount" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[shortcode_quiz_column_count]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_general_theme() {
        $val = Helper::resolve_theme_name( self::get_option( 'theme' ) );
        $themes = Helper::get_themes();

        $html = sprintf(
            '<select id="ddlTheme" name="%1$s[theme]" class="browser-default">',
            ARISTREAMQUIZ_SETTINGS_NAME
        );

        foreach ( $themes as $theme ) {
            $html .= sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr( $theme ),
                $theme == $val ? ' selected="selected"' : '',
                esc_html( $theme )
            );
        }

        $html .= '</select>';

        echo wp_kses(
            $html,
            array(
				'select' => array(
					'id' => true,
					'name' => true,
					'class' => true,
				),
				'option' => array(
					'value' => true,
					'selected' => true,
				),
            )
        );
    }

    public static function render_general_scroll_offset() {
        $val = self::get_option( 'scroll_offset' );

        printf(
            '<input type="number" class="input-small center-align" id="tbxScrollOffset" name="%1$s" value="%2$s" /> %3$s',
            esc_attr( sprintf( '%1$s[scroll_offset]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val ),
            esc_html__( 'pixels', 'ari-stream-quiz' )
        );
    }

    public static function render_general_scroll_duration() {
        $val = self::get_option( 'scroll_duration' );

        printf(
            '<input type="number" class="input-small center-align" id="tbxScrollDuration" min="0" name="%1$s" value="%2$s" /> %3$s',
            esc_attr( sprintf( '%1$s[scroll_duration]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val ),
            esc_html__( 'milliseconds', 'ari-stream-quiz' )
        );
    }

    public static function render_general_smart_scroll() {
        $val = self::get_option( 'smart_scroll' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkSmartScroll" name="%1$s" value="1"%2$s /><label for="chkSmartScroll"> </label>',
            esc_attr( sprintf( '%1$s[smart_scroll]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_general_show_questions_oncomplete() {
        $val = self::get_option( 'show_questions_oncomplete' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkShowQuestionsOnComplete" name="%1$s" value="1"%2$s /><label for="chkShowQuestionsOnComplete"> </label>',
            esc_attr( sprintf( '%1$s[show_questions_oncomplete]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_general_warning_on_exit() {
        $val = self::get_option( 'warning_on_exit' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkWarningOnExit" name="%1$s" value="1"%2$s /><label for="chkWarningOnExit"> </label>',
            esc_attr( sprintf( '%1$s[warning_on_exit]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_triviaquiz_lockout_answers() {
        $val = self::get_option( 'lockout_answers' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkLockoutAnswers" name="%1$s" value="1"%2$s /><label for="chkLockoutAnswers"> </label>',
            esc_attr( sprintf( '%1$s[lockout_answers]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_triviaquiz_show_results() {
        $val = self::get_option( 'show_results' );

        $html = sprintf(
            '<select id="ddlTriviaShowResults" name="%1$s[show_results]" class="browser-default">',
            ARISTREAMQUIZ_SETTINGS_NAME
        );

        $options = array(
            '' => esc_html__( 'No', 'ari-stream-quiz' ),

            'immediately' => esc_html__( 'Immediately after user answer', 'ari-stream-quiz' ),

            'on_complete' => esc_html__( 'When quiz is completed', 'ari-stream-quiz' ),
        );

        foreach ( $options as $key => $label ) {
            $html .= sprintf(
                '<option value="%1$s"%3$s>%2$s</option>',
                esc_attr( $key ),
                esc_html( $label ),
                $key == $val ? ' selected="selected"' : ''
            );
        }

        $html .= '</select>';

        echo wp_kses(
            $html,
            array(
                'select' => array(
                    'id' => true,
                    'name' => true,
                    'class' => true,
                ),
                'option' => array(
                    'value' => true,
                    'selected' => true,
                ),
            )
        );
    }

    public static function render_mailchimp_api_key() {
        $val = self::get_option( 'mailchimp_apikey' );

        printf(
            '<div><input type="text" id="tbxMailchimpKey" name="%1$s" value="%2$s" /></div><div class="right-align"><a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">%3$s</a></div>',
            esc_attr( sprintf( '%1$s[mailchimp_apikey]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val ),
            esc_html__( 'Where get API key?', 'ari-stream-quiz' )
        );
    }

    public static function render_mailerlite_api_key() {
        $val = self::get_option( 'mailerlite_apikey' );

        printf(
            '<div><input type="text" id="tbxMailerLiteKey" name="%1$s" value="%2$s" /></div><div class="right-align"><a href="https://app.mailerlite.com/subscribe/api" target="_blank">%3$s</a></div>',
            esc_attr( sprintf( '%1$s[mailerlite_apikey]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val ),
            esc_html__( 'Get API key', 'ari-stream-quiz' )
        );
    }

    public static function render_share_facebook_app_id() {
        $val = self::get_option( 'facebook_app_id' );

        printf(
            '<input type="text" id="tbxFacebookAppId" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[facebook_app_id]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_share_share_buttons() {
        $val = self::get_option( 'share_buttons' );

        $html = '';

        $share_buttons = array(
            'facebook' => __( 'Facebook', 'ari-stream-quiz' ),
            'twitter' => __( 'X (Twitter)', 'ari-stream-quiz' ),
            'email' => __( 'Email', 'ari-stream-quiz' ),
        );

        foreach ( $share_buttons as $share_button => $label ) {
            $html .= sprintf(
                '<div class="left checkbox-group-item"><input type="checkbox" class="filled-in" id="%5$s" name="%1$s" value="%2$s"%3$s /><label class="label" for="%5$s">%4$s</label></div>',
                esc_attr( sprintf( '%1$s[share_buttons][]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
                esc_attr( $share_button ),
                in_array( $share_button, $val ) ? ' checked="checked"' : '',
                esc_html( $label ),
                esc_attr( 'chkShareButton_' . $share_button )
            );
        }

        $allowed_html = array(
            'div' => array(
                'class' => true,
            ),
            'input' => array(
                'type' => true,
                'class' => true,
                'id' => true,
                'name' => true,
                'value' => true,
                'checked' => true,
            ),
            'label' => array(
                'class' => true,
                'for' => true,
            ),
        );
        echo '<div class="clearfix">' . wp_kses( $html, $allowed_html ) . '</div>';
    }

    public static function render_share_triviaquiz_title() {
        $val = self::get_option( 'share_trivia_title' );

        printf(
            '<input type="text" id="tbxShareTriviaTitle" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[share_trivia_title]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_facebook_title() {
        $val = self::get_option( 'share_trivia_facebook_title' );

        printf(
            '<input type="text" id="tbxShareTriviaFacebookTitle" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[share_trivia_facebook_title]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_facebook_content() {
        $val = self::get_option( 'share_trivia_facebook_content' );

        printf(
            '<input type="text" id="tbxShareTriviaFacebookContent" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[share_trivia_facebook_content]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_twitter_content() {
        $val = self::get_option( 'share_trivia_twitter_content' );

        printf(
            '<input type="text" id="tbxShareTriviaTwitterContent" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[share_trivia_twitter_content]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_email_subject() {
        $val = self::get_option( 'share_trivia_email_subject' );

        printf(
            '<input type="text" id="tbxShareTriviaEmailSubject" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[share_trivia_email_subject]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_email_body() {
        $val = self::get_option( 'share_trivia_email_body' );

        printf(
            '<input type="text" id="tbxShareTriviaEmailBody" name="%1$s" value="%2$s" />',
            esc_attr( sprintf( '%1$s[share_trivia_email_body]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function render_advanced_clean_uninstall() {
        $val = self::get_option( 'clean_uninstall' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkCleanUninstall" name="%1$s" value="1"%2$s /><label for="chkCleanUninstall"> </label>',
            esc_attr( sprintf( '%1$s[clean_uninstall]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_disable_script_optimization() {
        $val = self::get_option( 'disable_script_optimization' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkDisableScriptOptimization" name="%1$s" value="1"%2$s /><label for="chkDisableScriptOptimization"> </label>',
            esc_attr( sprintf( '%1$s[disable_script_optimization]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_facebook_load_sdk() {
        $val = self::get_option( 'facebook_load_sdk' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkFacebookLoadSDK" name="%1$s" value="1"%2$s /><label for="chkFacebookLoadSDK"> </label>',
            esc_attr( sprintf( '%1$s[facebook_load_sdk]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_lazy_load() {
        $val = self::get_option( 'lazy_load' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkLazyLoad" name="%1$s" value="1"%2$s /><label for="chkLazyLoad"> </label>',
            esc_attr( sprintf( '%1$s[lazy_load]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_add_meta_tags() {
        $val = self::get_option( 'add_meta_tags' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkAddMetaTags" name="%1$s" value="1"%2$s /><label for="chkAddMetaTags"> </label>',
            esc_attr( sprintf( '%1$s[add_meta_tags]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_custom_styles() {
        $val = self::get_option( 'custom_styles' );

        printf(
            '<textarea id="tbxCustomStyles" name="%1$s">%2$s</textarea>',
            esc_attr( sprintf( '%1$s[custom_styles]', ARISTREAMQUIZ_SETTINGS_NAME ) ),
            esc_attr( $val )
        );
    }

    public static function sanitize( $input ) {
        $new_input = array();

        foreach ( self::$default_settings as $key => $val ) {
            $type = gettype( $val );

            if ( 'boolean' == $type && ! isset( $input[ $key ] ) ) {
                $new_input[ $key ] = false;
            } else if ( 'array' == $type && ! isset( $input[ $key ] ) ) {
                $new_input[ $key ] = array();
            } else if ( isset( $input[ $key ] ) ) {
                $input_val = $input[ $key ];
                $filtered_val = null;
                switch ( $type ) {
                    case 'boolean':
                        $filtered_val = (bool) $input_val;
                        break;

                    case 'integer':
                        $filtered_val = intval( $input_val, 10 );
                        break;

                    case 'double':
                        $filtered_val = floatval( $input_val );
                        break;

                    case 'array':
                        $filtered_val = $input_val;
                        break;

                    case 'string':
                        $filtered_val = trim( $input_val );
                        break;
                }

                if ( ! is_null( $filtered_val ) ) {
                    $new_input[ $key ] = $filtered_val;
                }
            }
        }

        return $new_input;
    }
}
