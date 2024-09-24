<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

function asq_clear_site_data() {
    global $wpdb;

    $settings = get_option( 'ari_stream_quiz_settings' );

    if ( isset( $settings['clean_uninstall'] ) && (bool) $settings['clean_uninstall'] ) {
        $query = "DROP TABLE IF EXISTS `{$wpdb->prefix}asq_answers`,`{$wpdb->prefix}asq_questions`,`{$wpdb->prefix}asq_quizzes`,`{$wpdb->prefix}asq_result_templates`";
        // $wpdb->prefix is safe and %i format is only for WP 6.2+
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $wpdb->query( $query );

        delete_option( 'ari_stream_quiz_version' );
        delete_option( 'ari_stream_quiz_settings' );
    }
}

if ( ! is_multisite() ) {
    asq_clear_site_data( $queries );
} else {
    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $next_blog_id ) {
        switch_to_blog( $next_blog_id );

        asq_clear_site_data( $queries );
    }

    switch_to_blog( $original_blog_id );
}
