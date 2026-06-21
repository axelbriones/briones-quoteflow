<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_License {

    // Define your store URL here where EDD/Woo is installed
    const STORE_URL = 'https://your-plugin-store.com';
    const ITEM_NAME = 'Briones QuoteFlow Pro';

    public function __construct() {
        add_action( 'admin_init', array( $this, 'activate_license' ) );
        add_action( 'admin_init', array( $this, 'deactivate_license' ) );
    }

    public function activate_license() {
        if ( isset( $_POST['bqf_pro_license_activate'] ) ) {
            if ( ! check_admin_referer( 'bqf_pro_nonce', 'bqf_pro_nonce' ) ) {
                return;
            }

            // Capture the posted key if available (in case they didn't hit 'Save' first)
            if ( ! empty( $_POST['bqf_pro_license_key'] ) ) {
                $license = sanitize_text_field( trim( $_POST['bqf_pro_license_key'] ) );
                update_option( 'bqf_pro_license_key', $license );
            } else {
                $license = trim( get_option( 'bqf_pro_license_key' ) );
            }

            if ( empty( $license ) ) {
                return;
            }

            $api_params = array(
                'edd_action' => 'activate_license',
                'license'    => $license,
                'item_name'  => urlencode( self::ITEM_NAME ),
                'url'        => home_url()
            );

            $response = wp_remote_post( self::STORE_URL, array( 'timeout' => 15, 'body' => $api_params ) );

            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                $message = is_wp_error( $response ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'briones-quoteflow-pro' );
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_error', $message, 'error' );
                return;
            }

            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if ( false === $license_data || ! isset( $license_data->license ) ) {
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_error', __( 'Unexpected response from server.', 'briones-quoteflow-pro' ), 'error' );
                return;
            }

            update_option( 'bqf_pro_license_status', $license_data->license );

            if ( $license_data->license === 'valid' ) {
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_success', __( 'License activated successfully.', 'briones-quoteflow-pro' ), 'updated' );
            } else {
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_error', __( 'License could not be activated.', 'briones-quoteflow-pro' ), 'error' );
            }
        }
    }

    public function deactivate_license() {
        if ( isset( $_POST['bqf_pro_license_deactivate'] ) ) {
            if ( ! check_admin_referer( 'bqf_pro_nonce', 'bqf_pro_nonce' ) ) {
                return;
            }

            $license = trim( get_option( 'bqf_pro_license_key' ) );
            if ( empty( $license ) ) {
                return;
            }

            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license'    => $license,
                'item_name'  => urlencode( self::ITEM_NAME ),
                'url'        => home_url()
            );

            $response = wp_remote_post( self::STORE_URL, array( 'timeout' => 15, 'body' => $api_params ) );

            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                $message = is_wp_error( $response ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'briones-quoteflow-pro' );
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_error', $message, 'error' );
                return;
            }

            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if ( $license_data->license === 'deactivated' ) {
                delete_option( 'bqf_pro_license_status' );
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_success', __( 'License deactivated successfully.', 'briones-quoteflow-pro' ), 'updated' );
            } else {
                add_settings_error( 'bqf_pro_license_notices', 'bqf_pro_license_error', __( 'License could not be deactivated.', 'briones-quoteflow-pro' ), 'error' );
            }
        }
    }
}
