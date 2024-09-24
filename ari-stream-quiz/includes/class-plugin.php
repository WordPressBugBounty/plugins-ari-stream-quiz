<?php
namespace Ari_Stream_Quiz;

use Ari\App\Plugin as Ari_Plugin;
use Ari\Utils\Request;
use Ari\Wordpress\Helper as WP_Helper;
use Ari_Stream_Quiz\Helpers\Helper;
use Ari_Stream_Quiz\Helpers\Settings;
use Ari_Stream_Quiz\Helpers\Quizzes_Screen;

class Plugin extends Ari_Plugin {
    private $new_post_quiz_data = null;

    public function init() {
        $this->load_translations();

        add_action(
            'init',
            function () {
				$this->init_plugin();
			}
        );

        add_shortcode(
            'streamquiz',
            function ( $attrs ) {
				return $this->shortcode_handler( $attrs );
			}
        );

        if ( is_admin() ) {
            add_action(
                'admin_enqueue_scripts',
                function () {
					$this->admin_enqueue_scripts();
				}
            );
            add_action(
                'admin_menu',
                function () {
					$this->admin_menu();
				}
            );
            add_action(
                'admin_init',
                function () {
					$this->admin_init();
				}
            );
        } else {
            add_action(
                'wp_head',
                function () {
					$this->document_head();
				},
                1
            );
            add_action(
                'wp_enqueue_scripts',
                function () {
					$this->enqueue_scripts();
				}
            );
        }

        add_filter(
            'the_content',
            function ( $content ) {
				return $this->prepare_quiz_preview( $content );
			}
        );
        add_filter(
            'the_title',
            function ( $title, $post_id = null ) {
				return $this->prepare_quiz_title_preview( $title, $post_id );
			},
            10,
            2
        );
        add_filter(
            'set-screen-option',
            function ( $status, $option, $value ) {
                return Quizzes_Screen::set_options( $status, $option, $value );
            },
            10,
            3
        );

        parent::init();
    }

    private function load_translations() {
        load_plugin_textdomain( 'ari-stream-quiz', false, ARISTREAMQUIZ_SLUG . '/languages/override' );
        load_plugin_textdomain( 'ari-stream-quiz', false, ARISTREAMQUIZ_SLUG . '/languages' );
    }

    private function init_plugin() {
        $post_type_args = array(
            'description' => '',

            'public' => false,

            'rewrite' => false,

            'publicly_queryable' => true,

            'exclude_from_search' => true,

            'show_in_nav_menus' => false,

            'show_ui' => false,

            'show_in_menu' => false,

            'show_in_admin_bar' => false,

            'can_export' => false,

            'delete_with_user' => false,

            'hierarchical' => false,

            'has_archive' => false,

            'supports' => array(
                'title',
            ),

            'capability_type' => 'post',
        );

        register_post_type( ARISTREAMQUIZ_POST_TYPE, $post_type_args );

        if ( (bool) Settings::get_option( 'disable_script_optimization', false ) ) {
            add_filter(
                'script_loader_tag',
                function ( $script, $type ) {
					return $this->prepare_script( $script, $type );
				},
                10,
                2
            );
        }
    }

    private function prepare_quiz_title_preview( $title, $post_id ) {
        global $post;

        if ( $post && $post->ID === $post_id && is_user_logged_in() && ARISTREAMQUIZ_POST_TYPE == $post->post_type && is_main_query() && ! doing_action( 'wp_head' ) ) {
            $quiz_title = get_post_meta( $post->ID, 'quiz_title', true );

            return $quiz_title ? $quiz_title : $title;
        }

        return $title;
    }

    private function prepare_quiz_preview( $content ) {
        global $post;

        if ( $post && is_user_logged_in() && ARISTREAMQUIZ_POST_TYPE == $post->post_type && is_main_query() && ! doing_action( 'wp_head' ) ) {
            $quiz_id = get_post_meta( $post->ID, 'quiz_id', true );

            return $content . do_shortcode( '[streamquiz id="' . $quiz_id . '" hide_title="1" inline_scripts="1"]' );
        }

        return $content;
    }

    private function admin_menu() {
        $quizzes_pages = array();

        $quizzes_pages[] = add_menu_page(
            __( 'ARI Stream Quiz', 'ari-stream-quiz' ),
            __( 'ARI Stream Quiz', 'ari-stream-quiz' ),
            'edit_posts',
            'ari-stream-quiz',
            array( $this, 'display_quizzes' ),
            'dashicons-schedule'
        );

        $quizzes_pages[] = add_submenu_page(
            'ari-stream-quiz',
            __( 'Quizzes', 'ari-stream-quiz' ),
            __( 'Quizzes', 'ari-stream-quiz' ),
            'edit_posts',
            'ari-stream-quiz-quizzes',
            array( $this, 'display_quizzes' )
        );

        add_submenu_page(
            'ari-stream-quiz',
            __( 'Settings', 'ari-stream-quiz' ),
            __( 'Settings', 'ari-stream-quiz' ),
            'manage_options',
            'ari-stream-quiz-settings',
            array( $this, 'display_settings' )
        );

        // Hidden pages
		add_submenu_page(
            null,
            '',
            '',
            'edit_posts',
            'ari-stream-quiz-quiz',
            array( $this, 'display_quiz' )
        );

		remove_submenu_page( 'ari-stream-quiz', 'ari-stream-quiz' );

        foreach ( $quizzes_pages as $quizzes_page ) {
            add_action(
                'load-' . $quizzes_page,
                function () {
					Quizzes_Screen::register();
				}
            );
        }

        add_filter(
            'parent_file',
            function ( $file ) {
				global $plugin_page;

				if ( $plugin_page && strpos( $plugin_page, 'ari-stream-quiz' ) !== 0 ) {
					return $file;
				}

				switch ( $plugin_page ) {
					case 'ari-stream-quiz-quiz':
                        // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$plugin_page = 'ari-stream-quiz-quizzes';
						break;
				}

				return $file;
			}
        );
    }

	private function admin_enqueue_scripts() {
		$options = $this->options;

        wp_register_script( 'ari-materialize', $options->assets_url . 'materialize/js/materialize.min.js', array( 'jquery' ), $options->version );
        wp_register_style( 'ari-streamquiz-materialize', $options->assets_url . 'materialize/css/materialize.min.css', array(), $options->version );
        wp_register_style( 'ari-materialize-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array() );

        wp_register_script( 'ari-streamquiz-app', $options->assets_url . 'common/app.js', array( 'jquery' ), $options->version );
        wp_register_script( 'ari-scrollto', $options->assets_url . 'scroll_to/jquery.scrollTo.min.js', array( 'jquery' ), $options->version );
        wp_register_script( 'ari-cloner', $options->assets_url . 'cloner/js/jquery.cloner.min.js', array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable', 'ari-scrollto' ), $options->version );
        wp_register_script( 'ari-cloner-ext', $options->assets_url . 'common/cloner.ext.js', array( 'ari-cloner' ), $options->version );
        wp_register_script( 'ari-smart-dropdown', $options->assets_url . 'common/smart_dropdown.js', array( 'jquery' ), $options->version );

        wp_register_script( 'ari-clipboard', $options->assets_url . 'clipboard/clipboard.min.js', array(), $options->version );

        wp_register_script( 'ari-trumbowyg-editor', $options->assets_url . 'trumbowyg/js/trumbowyg.min.js', array(), $options->version );
        wp_register_script( 'ari-trumbowyg-langs', $options->assets_url . 'trumbowyg/js/langs/all.min.js', array( 'ari-trumbowyg-editor' ), $options->version );
        wp_register_script( 'ari-trumbowyg', $options->assets_url . 'trumbowyg/js/init.js', array( 'ari-trumbowyg-editor', 'ari-trumbowyg-langs' ), $options->version );
        wp_register_style( 'ari-trumbowyg', $options->assets_url . 'trumbowyg/css/trumbowyg.min.css', array(), $options->version );

        $trumbowyg_options = array(
            'lang' => get_locale(),

            'svgPath' => $options->assets_url . 'trumbowyg/js/ui/icons.svg',
        );
        $cloner_options = array(
            'messages' => array(
                'removeItem' => __( 'Are you sure?', 'ari-stream-quiz' ),
            ),
        );
        wp_localize_script( 'ari-trumbowyg', 'ARI_TRUMBOWYG', $trumbowyg_options );
        wp_localize_script( 'ari-cloner-ext', 'ARI_QUIZ_CLONER', $cloner_options );
	}

    private function enqueue_scripts() {
        $options = $this->options;

		wp_register_style( 'ari-quiz-theme', ARISTREAMQUIZ_URL . 'themes/assets/css/theme.css', array(), ARISTREAMQUIZ_VERSION );

        wp_register_script( 'ari-scrollto', $options->assets_url . 'scroll_to/jquery.scrollTo.min.js', array( 'jquery' ), $options->version );
        wp_register_script( 'ari-quiz', $options->assets_url . 'common/jquery.quiz.js', array( 'jquery', 'ari-scrollto' ), $options->version );
    }

    private function shortcode_handler( $attrs ) {
        if ( empty( $attrs['id'] ) ) {
            return __( 'Please specify a quiz ID', 'ari-stream-quiz' );
        }

        $options = array(
            'model_options' => array(
                'state' => $attrs,
            ),
        );

        ob_start();

		$this->display_quiz_session(
            $options,
            array(
				'action' => 'display',
				'page' => 'quiz_session',
            )
        );

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    private function admin_init() {
        Settings::init();

        add_filter(
            'admin_title',
            function () {
				return $this->prepare_titles();
			}
        );

        add_filter(
            'default_content',
            function ( $content ) {
				return $this->prepare_new_post_content( $content );
			}
        );
        add_filter(
            'default_title',
            function ( $title ) {
				return $this->prepare_new_post_title( $title );
			}
        );

        $no_header = (bool) Request::get_var( 'noheader' );

        if ( ! $no_header ) {
            $page = Request::get_var( 'page' );

            if ( $page && 0 === strpos( $page, 'ari-stream-quiz' ) ) {
                ob_start();

                add_action(
                    'admin_page_' . $page,
                    function () {
						ob_end_flush();
					},
                    99
                );
            }
        }
    }

    private function prepare_titles() {
        global $admin_title;

        $title = $admin_title;

        $page = Request::get_var( 'page' );
        $action = Request::get_var( 'action' );

        switch ( $page ) {
            case 'ari-stream-quiz-quiz':
                $title = ( 'edit' == $action ? __( 'Edit Quiz', 'ari-stream-quiz' ) : __( 'Add New Quiz', 'ari-stream-quiz' ) ) . $title;
                break;
        }

        return $title;
    }

    private function get_new_post_quiz_data() {
        if ( ! is_null( $this->new_post_quiz_data ) ) {
            return $this->new_post_quiz_data;
        }

        $quiz_data = array(
            'id' => 0,

            'title' => '',
        );

        if ( Request::exists( 'stream_quiz' ) ) {
            $req_quiz_data = Request::get_var( 'stream_quiz', array() );

            if ( ! empty( $req_quiz_data['id'] ) ) {
                $quiz_id = intval( $req_quiz_data['id'], 10 );

                if ( $quiz_id > 0 ) {
                    $quiz_data['id'] = $quiz_id;
                }
            }

            if ( isset( $req_quiz_data['title'] ) ) {
                $quiz_data['title'] = $req_quiz_data['title'];
            }
        }

        $this->new_post_quiz_data = $quiz_data;

        return $this->new_post_quiz_data;
    }

    private function prepare_new_post_title( $title ) {
        $quiz_data = $this->get_new_post_quiz_data();

        if ( $quiz_data['id'] < 1 ) {
            return $title;
        }

        $title .= $quiz_data['title'];

        return $title;
    }

    private function prepare_new_post_content( $content ) {
        $quiz_data = $this->get_new_post_quiz_data();

        if ( $quiz_data['id'] < 1 ) {
            return $content;
        }

        $content .= sprintf(
            '[streamquiz id="%1$d"]',
            $quiz_data['id']
        );

        return $content;
    }

    protected function need_to_update() {
        $installed_version = get_option( ARISTREAMQUIZ_VERSION_OPTION );

        return ( $installed_version != $this->options->version );
    }

    protected function install() {
        $installer = new \Ari_Stream_Quiz\Installer();

        return $installer->run();
    }

    private function document_head() {
        $add_meta_tags = (bool) Settings::get_option( 'add_meta_tags', true );

        if ( $add_meta_tags ) {
            $this->print_quiz_meta_tags();
        }
    }

    private function print_quiz_meta_tags() {
        if ( ! is_singular() ) {
            return;
        }

        global $post;

        if ( empty( $post ) || ! has_shortcode( $post->post_content, 'streamquiz' ) ) {
            return;
        }

        $meta_title = get_the_title();
        $twitter_description = null;
        $og_description = null;
        $meta_domain = site_url();
        $meta_url = get_permalink();
        $meta_thumb_url = null;

        if ( strlen( $post->post_excerpt ) > 0 ) {
            $twitter_description = $post->post_excerpt;
            $og_description = $post->post_excerpt;
        }

        // check Yoast SEO
        if ( defined( 'WPSEO_VERSION' ) ) {
            global $wpseo_og;

            if ( $wpseo_og ) {
                remove_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ), 30 );
            }

            remove_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );

            $og_description = get_post_meta( $post->ID, '_yoast_wpseo_opengraph-description', true );
            $twitter_description = get_post_meta( $post->ID, '_yoast_wpseo_twitter-description', true );
        }

        if ( has_post_thumbnail() ) {
            $wp_attach_meta = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full', true )[0];
            if ( is_array( $wp_attach_meta ) && count( $wp_attach_meta ) > 0 ) {
                $meta_thumb_url = $wp_attach_meta[0];
            }
        }

        if ( strlen( $twitter_description ) == 0 || strlen( $og_description ) == 0 || strlen( $meta_thumb_url ) == 0 ) {
            $matches = null;
            if ( preg_match( '/\[streamquiz\s+[^]]*id="?([\s\d]+)/s', $post->post_content, $matches ) ) {
                $quiz_id = intval( trim( $matches[1] ), 10 );
                if ( $quiz_id > 0 ) {
                    $meta_tags = Helper::get_quiz_meta_tags( $quiz_id );
                    if ( ! empty( $meta_tags ) ) {
                        if ( strlen( $twitter_description ) == 0 ) {
                            $twitter_description = $meta_tags->quiz_description;
                        }

                        if ( strlen( $og_description ) == 0 ) {
                            $og_description = $meta_tags->quiz_description;
                        }

                        if ( strlen( $meta_thumb_url ) == 0 && $meta_tags->quiz_image_id > 0 ) {
                            $wp_attach_meta = wp_get_attachment_image_src( $meta_tags->quiz_image_id, 'full', true );
                            if ( is_array( $wp_attach_meta ) && count( $wp_attach_meta ) > 0 ) {
                                $meta_thumb_url = $wp_attach_meta[0];
                            }
                        }
                    }
                }
            }
        }

        $meta_title = WP_Helper::extract_text( $meta_title );
        $twitter_description = WP_Helper::extract_text( $twitter_description );
        $og_description = WP_Helper::extract_text( $og_description );

        printf(
            '<meta name="twitter:title" content="%1$s">' .
            '<meta name="twitter:description" content="%2$s">' .
            '<meta name="twitter:domain" content="%3$s">' .
            '<meta name="og:title" content="%1$s">' .
            '<meta name="og:description" content="%4$s">' .
            '<meta name="og:url" content="%5$s">',
            esc_attr( $meta_title ),
            esc_attr( $twitter_description ),
            esc_url( $meta_domain ),
            esc_attr( $og_description ),
            esc_url( $meta_url )
        );

        if ( $meta_thumb_url ) {
            printf(
                '<meta name="twitter:card" content="summary_large_image">' .
                '<meta name="twitter:image:src" content="%1$s">' .
                '<meta property="og:image" content="%1$s" />' .
                '<meta itemprop="image" content="%1$s">',
                esc_url( $meta_thumb_url )
            );
        }
    }

    private function prepare_script( $script, $type ) {
        if ( strpos( $script, ARISTREAMQUIZ_URL ) === false ) {
            return $script;
        }

        $script = str_replace( ' src=', ' data-cfasync="false" src=', $script );

        return $script;
    }
}
