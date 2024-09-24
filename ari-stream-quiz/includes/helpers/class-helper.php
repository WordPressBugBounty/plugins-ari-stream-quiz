<?php
namespace Ari_Stream_Quiz\Helpers;

use Ari_Stream_Quiz\Helpers\Settings;
use Ari\Utils\Array_Helper;

define( 'ARI_STREAM_QUIZ_CACHE_LIFETIME', 10 * MINUTE_IN_SECONDS );

class Helper {
    private static $system_args = array(
        'action',

        'msg',

        'msg_type',

        'noheader',
    );

    private static $quiz_types = array(
        ARISTREAMQUIZ_QUIZTYPE_TRIVIA,
    );

    private static $themes = null;

    public static function build_url( $add_args = array(), $remove_args = array(), $remove_system_args = true, $encode_args = true ) {
        if ( $remove_system_args ) {
            $remove_args = array_merge( $remove_args, self::$system_args );
        }

        if ( $encode_args ) {
            $add_args = array_map( 'rawurlencode', $add_args );
        }

        return add_query_arg( $add_args, remove_query_arg( $remove_args ) );
    }

    public static function is_valid_quiz_type( $type ) {
        return $type && in_array( $type, self::$quiz_types );
    }

    public static function get_themes() {
        if ( ! is_null( self::$themes ) ) {
            return self::$themes;
        }

        $folders = array();
        $path = ARISTREAMQUIZ_THEMES_PATH;
        $exclude = array( 'assets' );

        $handle = @opendir( $path );
        if ( ! $handle ) {
            return $folders;
        }

        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( '.' == $file || '..' == $file || in_array( $file, $exclude ) ) {
                continue;
            }

            $is_dir = is_dir( $path . $file );

            if ( ! $is_dir ) {
                continue;
            }

            $folders[] = $file;
        }

        self::$themes = $folders;

        return self::$themes;
    }

    public static function resolve_theme_name( $theme ) {
        $themes = self::get_themes();

        if ( ! in_array( $theme, $themes ) ) {
            $theme = ARISTREAMQUIZ_THEME_DEFAULT;
        }

        return $theme;
    }

    public static function quiz_type_nicename( $quiz_type ) {
        $nicename = '';

		if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $quiz_type ) {
            $nicename = __( 'Trivia', 'ari-stream-quiz' );
        }

        return $nicename;
    }

    public static function can_edit_other_quizzes() {
        return current_user_can( 'edit_others_posts' );
    }

    public static function can_edit_quiz( $quiz_id ) {
        if ( self::can_edit_other_quizzes() ) {
            return true;
        }

        $can_edit = false;

        $quiz_id = intval( $quiz_id, 10 );
        if ( $quiz_id < 1 ) {
            return $can_edit;
        }

        $quizzes_model = new \Ari_Stream_Quiz\Models\Quizzes(
            array(
                'class_prefix' => 'Ari_Stream_Quiz',
            )
        );
        $quiz_author_id = $quizzes_model->get_quiz_author_id( $quiz_id );
        if ( $quiz_author_id > 0 && get_current_user_id() == $quiz_author_id ) {
            $can_edit = true;
        }

        return $can_edit;
    }

    public static function filter_edit_quizzes( $id_list ) {
        if ( self::can_edit_other_quizzes() ) {
            return $id_list;
        }

        $id_list = \Ari\Utils\Array_Helper::to_int( $id_list, 1 );

        if ( count( $id_list ) == 0 ) {
            return $id_list;
        }

        $quizzes_model = new \Ari_Stream_Quiz\Models\Quizzes(
            array(
                'class_prefix' => 'Ari_Stream_Quiz',
            )
        );
        $quizzes_author_id = $quizzes_model->get_quizzes_author_id( $id_list );

        $filter_id_list = array();
        $user_id = get_current_user_id();

        foreach ( $id_list as $quiz_id ) {
            if ( isset( $quizzes_author_id[ $quiz_id ] ) ) {
                $quiz_author_id = $quizzes_author_id[ $quiz_id ]->author_id;

                if ( $user_id == $quiz_author_id ) {
                    $filter_id_list[] = $quiz_id;
                }
            }
        }

        return $filter_id_list;
    }

    public static function get_mailchimp_lists( $reload = false ) {
        $api_key = Settings::get_option( 'mailchimp_apikey' );

        if ( empty( $api_key ) ) {
            return array();
        }

        $cache_key = md5( 'mailchimp_lists_' . $api_key );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $lists = array();
        try {
            $mailchimp = new \DrewM\MailChimp\MailChimp( $api_key );

            $result = $mailchimp->get(
                'lists',
                array(
                    'fields' => 'lists.id,lists.name',

                    'count' => 9999,
                )
            );

            if ( ! empty( $result['lists'] ) && is_array( $result['lists'] ) ) {
                foreach ( $result['lists'] as $list ) {
                    $list_obj = new \stdClass();
                    $list_obj->id = $list['id'];
                    $list_obj->name = $list['name'];

                    $lists[] = $list_obj;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch ( \Exception $ex ) {
            $lists = array();
        }

        return $lists;
    }

    public static function get_mailerlite_lists( $reload = false ) {
        $api_key = Settings::get_option( 'mailerlite_apikey' );

        if ( empty( $api_key ) ) {
            return array();
        }

        $cache_key = md5( 'mailerlite_lists_' . $api_key );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $lists = array();
        try {
            $groups_api = ( new \MailerLiteApi\MailerLite( $api_key ) )->groups();

            $groups = $groups_api->get();

            if ( ! empty( $groups ) && $groups->count() > 0 ) {
                foreach ( $groups as $list ) {
                    $list_obj = new \stdClass();
                    $list_obj->id = $list->id;
                    $list_obj->name = $list->name;

                    $lists[] = $list_obj;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch ( \Exception $ex ) {
            $lists = array();
        }

        return $lists;
    }

    public static function get_quiz_meta_tags( $quiz_id ) {
        $quiz_id = intval( $quiz_id, 10 );
        if ( $quiz_id < 1 ) {
            return null;
        }

        $quiz_model = new \Ari_Stream_Quiz\Models\Quiz(
            array(
                'class_prefix' => 'Ari_Stream_Quiz',
            )
        );

        return $quiz_model->get_quiz_meta_tags( $quiz_id );
    }
}
