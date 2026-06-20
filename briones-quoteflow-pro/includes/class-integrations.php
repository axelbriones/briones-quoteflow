<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_Integrations {

    public function __construct() {
        add_action( 'bqf_after_quote_created', array( $this, 'trigger_integrations' ), 15, 2 );
    }

    public function trigger_integrations( $quote_id, $data ) {
        // Fetch full quote details from DB
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';
        $quote = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $quote_id ), ARRAY_A );

        if ( ! $quote ) {
            return;
        }

        // Trigger Google Sheets
        if ( get_option( 'bqf_pro_gsheets_active', 0 ) ) {
            $this->send_to_gsheets( $quote_id, $quote );
        }

        // Trigger HubSpot
        if ( get_option( 'bqf_pro_hubspot_active', 0 ) ) {
            $this->send_to_hubspot( $quote_id, $quote );
        }
    }

    private function send_to_gsheets( $quote_id, $quote ) {
        $webhook_url = get_option( 'bqf_pro_gsheets_url', '' );
        if ( empty( $webhook_url ) ) {
            return;
        }

        $payload = wp_json_encode( $quote );

        $args = array(
            'body'        => $payload,
            'headers'     => array(
                'Content-Type' => 'application/json',
            ),
            'timeout'     => 15,
            'blocking'    => true,
        );

        $response = wp_remote_post( $webhook_url, $args );

        if ( class_exists( 'BQF_Logger' ) ) {
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                BQF_Logger::log_timeline( $quote_id, "Google Sheets Sync Failed: {$error_message}" );
            } else {
                $status_code = wp_remote_retrieve_response_code( $response );
                if ( $status_code >= 200 && $status_code < 300 ) {
                    BQF_Logger::log_timeline( $quote_id, "Google Sheets Sync Successful" );
                } else {
                    BQF_Logger::log_timeline( $quote_id, "Google Sheets Sync Error (HTTP {$status_code})" );
                }
            }
        }
    }

    private function send_to_hubspot( $quote_id, $quote ) {
        $token = get_option( 'bqf_pro_hubspot_token', '' );
        if ( empty( $token ) ) {
            return;
        }

        // Map data to HubSpot Contact Properties
        $properties = array(
            'email'     => $quote['email'],
            'firstname' => $quote['name'],
            'phone'     => $quote['phone'],
            'company'   => $quote['company'],
            'message'   => "Quote Request: " . $quote['product_name'] . "\n\n" . $quote['message']
        );

        $payload = wp_json_encode( array(
            'properties' => $properties
        ) );

        $args = array(
            'body'        => $payload,
            'headers'     => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ),
            'timeout'     => 15,
            'blocking'    => true,
        );

        $response = wp_remote_post( 'https://api.hubapi.com/crm/v3/objects/contacts', $args );

        if ( class_exists( 'BQF_Logger' ) ) {
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                BQF_Logger::log_timeline( $quote_id, "HubSpot Sync Failed: {$error_message}" );
            } else {
                $status_code = wp_remote_retrieve_response_code( $response );
                if ( $status_code == 200 || $status_code == 201 ) {
                    BQF_Logger::log_timeline( $quote_id, "HubSpot Sync Successful (Contact Created/Updated)" );
                } elseif ( $status_code == 409 ) {
                    BQF_Logger::log_timeline( $quote_id, "HubSpot Sync: Contact already exists." );
                } else {
                    BQF_Logger::log_timeline( $quote_id, "HubSpot Sync Error (HTTP {$status_code})" );
                }
            }
        }
    }
}
