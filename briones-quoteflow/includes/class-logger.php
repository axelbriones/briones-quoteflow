<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Logger {

    public static function log_timeline( $quote_id, $action ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';

        $quote = $wpdb->get_row( $wpdb->prepare( "SELECT timeline FROM $table_name WHERE id = %d", $quote_id ) );
        if ( ! $quote ) {
            return false;
        }

        $timeline = json_decode( $quote->timeline, true );
        if ( ! is_array( $timeline ) ) {
            $timeline = array();
        }

        $current_user = wp_get_current_user();
        $user_name = $current_user->exists() ? $current_user->display_name : 'System';

        $timeline[] = array(
            'time'   => current_time( 'mysql' ),
            'action' => sanitize_text_field( $action ),
            'user'   => sanitize_text_field( $user_name )
        );

        return $wpdb->update(
            $table_name,
            array(
                'timeline'   => wp_json_encode( $timeline ),
                'updated_at' => current_time( 'mysql' )
            ),
            array( 'id' => $quote_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );
    }
}
