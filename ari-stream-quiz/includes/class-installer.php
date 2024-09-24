<?php
namespace Ari_Stream_Quiz;

use Ari\App\Installer as Ari_Installer;
use Ari\Database\Helper as DB;

class Installer extends Ari_Installer {
    public function __construct( $options = array() ) {
        if ( ! isset( $options['installed_version'] ) ) {
            $installed_version = get_option( ARISTREAMQUIZ_VERSION_OPTION );

            if ( false !== $installed_version ) {
                $options['installed_version'] = $installed_version;
            }
        }

        if ( ! isset( $options['version'] ) ) {
            $options['version'] = ARISTREAMQUIZ_VERSION;
        }

        parent::__construct( $options );
    }

    private function init() {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $sql = file_get_contents( ARISTREAMQUIZ_INSTALL_PATH . 'install.sql' );
        $utf8mb4_supported = DB::is_utf8mb4_supported();

        if ( ! $utf8mb4_supported ) {
            $sql = str_replace( 'utf8mb4_unicode_ci', 'utf8_general_ci', $sql );
            $sql = str_replace( 'utf8mb4', 'utf8', $sql );
        }

        $queries = DB::split_sql( $sql );

        foreach ( $queries as $query ) {
            $this->db->query( $query );
        }
    }

    public function run() {
        $this->init();

        if ( ! $this->run_versions_updates() ) {
            return false;
        }

        update_option( ARISTREAMQUIZ_VERSION_OPTION, $this->options->version );

        return true;
    }
}
