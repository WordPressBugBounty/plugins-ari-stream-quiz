<?php
namespace Ari\Wordpress;

use Ari\Utils\Array_Helper;
use Ari\Utils\Utils;

final class Helper {
    public static function get_attachment_url( $id ) {
        return wp_get_attachment_url( $id );
    }

    public static function get_attachment_list( $id_list, $url_type = null ) {
        global $wpdb;

        $id_list = Array_Helper::to_int( Array_Helper::ensure_array( $id_list ), 1 );

        if ( count( $id_list ) == 0 ) {
            return false;
        }

        $query = sprintf(
            'SELECT PM.post_id,P.post_mime_type,P.post_type,P.guid,P.post_title,P.post_content,P.post_excerpt,PM.meta_key,PM.meta_value FROM `%1$sposts` P INNER JOIN `%1$spostmeta` PM ON P.ID = PM.post_id WHERE P.ID IN (%2$s) ORDER BY P.ID ASC',
            $wpdb->prefix,
            join( ',', $id_list )
        );

        $attachments = $wpdb->get_results(
            // $wpdb->prefix is safe value and all table names are quoted in the query. $id_list contains only integers and it is used in IN
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $query,
            OBJECT
        );

        $current_post_id  = 0;
        $result_attachments = array();
        foreach ( $attachments as $attachment ) {
            $post_id = $attachment->post_id;

            // next post
            $post = null;
            if ( $current_post_id !== $post_id ) {
                $post = new \stdClass();

                $post->post_id = $post_id;
                $post->post_type = $attachment->post_type;
                $post->mime_type = $attachment->post_mime_type;
                $post->guid = $attachment->guid;
                $post->title = $attachment->post_title;
                $post->description = $attachment->post_content;
                $post->caption = $attachment->post_excerpt;

                $result_attachments[ $post_id ] = $post;

                $current_post_id = $post_id;
            } else {
                $post = $result_attachments[ $post_id ];
            }

            $meta_key = $attachment->meta_key;
            $meta_val = maybe_unserialize( $attachment->meta_value );

            $post->$meta_key = $meta_val;
        }

        foreach ( $result_attachments as $result_attachment ) {
            $result_attachment->url = self::get_attachment_post_url( $result_attachment, $url_type );
            $metadata = Utils::get_value( $result_attachment, '_wp_attachment_metadata' );
            $sizes_metadata = Utils::get_value( $metadata, 'sizes' );

            $width = 0;
            $height = 0;

            if ( ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
                if ( ! empty( $url_type ) &&
                    ! empty( $sizes_metadata[ $url_type ] ) &&
                    ! empty( $sizes_metadata[ $url_type ]['width'] ) &&
                    ! empty( $sizes_metadata[ $url_type ]['height'] )
                ) {
                    $width = $sizes_metadata[ $url_type ]['width'];
                    $height = $sizes_metadata[ $url_type ]['height'];
                } else {
                    $width = $metadata['width'];
                    $height = $metadata['height'];
                }
            }

            $result_attachment->width = $width;
            $result_attachment->height = $height;
        }

        return $result_attachments;
    }

    private static function get_attachment_post_url( $post, $url_type = null ) {
        if ( 'attachment' != $post->post_type ) {
            return false;
        }

        if ( ! is_null( $url_type ) ) {
            $url = self::get_attachment_post_url( $post );

            $metadata = Utils::get_value( $post, '_wp_attachment_metadata' );
            $sizes = Utils::get_value( $metadata, 'sizes' );
            $size_data = Utils::get_value( $sizes, $url_type );
            if ( $size_data ) {
                $url_basename = wp_basename( $url );
                $url = str_replace( $url_basename, $size_data['file'], $url );
            }

            return $url;
        }

        $file = Utils::get_value( $post, '_wp_attached_file' );
        if ( $file ) {
            $uploads = wp_upload_dir( null, false );
            if ( $uploads && false === $uploads['error'] ) {
                if ( 0 === strpos( $file, $uploads['basedir'] ) ) {
                    $url = str_replace( $uploads['basedir'], $uploads['baseurl'], $file );
                } elseif ( false !== strpos( $file, 'wp-content/uploads' ) ) {
                    $url = trailingslashit( $uploads['baseurl'] . '/' . _wp_get_attachment_relative_path( $file ) ) . basename( $file );
                } else {
                    $url = $uploads['baseurl'] . "/$file";
                }
            }
        }

        if ( empty( $url ) ) {
            $url = $post->guid;
        }

        if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $GLOBALS['pagenow'] ) {
            $url = set_url_scheme( $url );
        }

        $url = apply_filters( 'wp_get_attachment_url', $url, $post->post_id );

        if ( empty( $url ) ) {
            return false;
        }

        return $url;
    }

    public static function process_embed_media( $content ) {
        global $wp_embed;

        if ( $wp_embed ) {
            $content = $wp_embed->run_shortcode( $content );
        }

        return $content;
    }

    public static function do_shortcode( $content, $extended = true ) {
        if ( $extended ) {
            $content = self::process_embed_media( $content );
        }

        $content = do_shortcode( $content );

        return $content;
    }

    public static function extract_text( $content, $remove_new_lines = true ) {
        if ( strlen( $content ) === 0 ) {
            return '';
        }

        $content = trim( strip_shortcodes( wp_strip_all_tags( $content ) ) );
        if ( $remove_new_lines ) {
            $content = str_replace( array( "\r", "\n" ), '', $content );
        }

        return $content;
    }

    public static function is_local_url( $url ) {
        $base_url = get_site_url();

        return strpos( $url, $base_url ) === 0;
    }

    public static function url_to_path( $url ) {
        $path = $url;
        if ( self::is_local_url( $url ) ) {
            $base_url = get_site_url();
            $path = wp_normalize_path( ABSPATH . str_replace( '/', DIRECTORY_SEPARATOR, str_replace( $base_url, '', $url ) ) );
        }

        return $path;
    }
}
