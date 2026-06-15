<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Briones_QuoteFlow
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Optionally delete options
delete_option( 'bqf_notification_email' );
delete_option( 'bqf_success_message' );
delete_option( 'bqf_button_text' );
delete_option( 'bqf_enable_modal' );
delete_option( 'bqf_enable_price' );
delete_option( 'bqf_catalog_mode' );
delete_option( 'bqf_field_company' );
delete_option( 'bqf_field_phone' );
delete_option( 'bqf_field_message' );

// Optionally drop custom database table (commented out by default to prevent accidental data loss,
// but fully ready if you want strict cleanup).
/*
global $wpdb;
$table_name = $wpdb->prefix . 'bqf_quotes';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
*/
