<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Briones_QuoteFlow_Pro
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Cleanup Pro-specific options here (e.g., license keys)
// delete_option( 'bqf_pro_license_key' );
// delete_option( 'bqf_pro_license_status' );

// DO NOT DROP THE CORE DB TABLE (`wp_bqf_quotes`).
// That is owned by the Free plugin.
