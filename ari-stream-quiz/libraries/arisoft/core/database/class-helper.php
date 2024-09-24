<?php
namespace Ari\Database;

class Helper {
    public static function split_sql( $sql, $prepare_query = true ) {
        $start = 0;
        $open = false;
        $comment = false;
        $end_string = '';
        $end = strlen( $sql );
        $queries = array();
        $query = '';

        for ( $i = 0; $i < $end; $i++ ) {
            $current = substr( $sql, $i, 1 );
            $current2 = substr( $sql, $i, 2 );
            $current3 = substr( $sql, $i, 3 );
            $len_end_string = strlen( $end_string );
            $test_end = substr( $sql, $i, $len_end_string );

            if ( '"' == $current || "'" == $current || '--' == $current2
                || ( '/*' == $current2 && '/*!' != $current3 && '/*+' != $current3 )
                || ( '#' == $current && '#__' != $current3 )
                || ( $comment && $test_end == $end_string ) ) {

                $n = 2;

                while ( substr( $sql, $i - $n + 1, 1 ) == '\\' && $n < $i ) {
                    $n++;
                }

                if ( 0 == $n % 2 ) {
                    if ( $open ) {
                        if ( $test_end == $end_string ) {
                            if ( $comment ) {
                                $comment = false;
                                if ( $len_end_string > 1 ) {
                                    $i += ( $len_end_string - 1 );
                                    $current = substr( $sql, $i, 1 );
                                }
                                $start = $i + 1;
                            }
                            $open = false;
                            $end_string = '';
                        }
                    } else {
                        $open = true;
                        if ( '--' == $current2 ) {
                            $end_string = "\n";
                            $comment = true;
                        } elseif ( '/*' == $current2 ) {
                            $end_string = '*/';
                            $comment = true;
                        } elseif ( '#' == $current ) {
                            $end_string = "\n";
                            $comment = true;
                        } else {
                            $end_string = $current;
                        }

                        if ( $comment && $start < $i ) {
                            $query = $query . substr( $sql, $start, ( $i - $start ) );
                        }
                    }
                }
            }

            if ( $comment ) {
                $start = $i + 1;
            }

            if ( ( ';' == $current && ! $open ) || $i == $end - 1 ) {
                if ( $start <= $i ) {
                    $query = $query . substr( $sql, $start, ( $i - $start + 1 ) );
                }
                $query = trim( $query );

                if ( $query ) {
                    if ( ( $i == $end - 1 ) && ( ';' != $current ) ) {
                        $query = $query . ';';
                    }

                    if ( $prepare_query ) {
                        $query = self::prepare_query( $query );
                    }

                    $queries[] = $query;
                }

                $query = '';
                $start = $i + 1;
            }
        }

        return $queries;
    }

    public static function prepare_query( $query ) {
        global $wpdb;

        $query = str_replace(
            '#__',
            $wpdb->prefix,
            $query
        );

        return $query;
    }

    public static function quote_identifier( $identifier ) {
        return '`' . self::escape_identifier_value( $identifier ) . '`';
    }

    private static function escape_identifier_value( $identifier ) {
		return str_replace( '`', '``', $identifier );
	}

    public static function column_exists( $table, $column ) {
        global $wpdb;

        $table = self::quote_identifier( $table );
        $query = $wpdb->prepare(
            // table name is quoted and use the same approach as %i format in WP 6.2+
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SHOW COLUMNS FROM {$table} LIKE %s",
            $column
        );
        $query = self::prepare_query( $query );

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $columns = $wpdb->get_row( $query, ARRAY_N );

        return ( ! empty( $columns ) && count( $columns ) > 0 );
    }

    public static function index_exists( $table, $index ) {
        global $wpdb;

        $table = self::quote_identifier( $table );
        $query = "SHOW INDEX FROM {$table}";
        $query = self::prepare_query( $query );

        $keys = $wpdb->get_results(
            // table name is quoted and use the same approach as %i format in WP 6.2+
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $query,
            ARRAY_A
        );
        if ( is_array( $keys ) ) {
            foreach ( $keys as $key_info ) {
                if ( isset( $key_info['Key_name'] ) && $key_info['Key_name'] == $index ) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function is_utf8mb4_supported() {
        global $wpdb;

        $res = $wpdb->get_var( 'SELECT COUNT(*) FROM information_schema.character_sets WHERE `CHARACTER_SET_NAME` = "utf8mb4"' );

        return ! ! $res;
    }
}
