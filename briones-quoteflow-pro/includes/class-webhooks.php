<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_Webhooks {

    public function __construct() {
        add_action( 'bqf_after_quote_created', array( $this, 'trigger_webhook' ), 10, 2 );
    }

    /**
     * Dispatches the webhook payload when a new quote is created.
     *
     * @param int   $quote_id The database ID of the newly inserted quote.
     * @param array $data     The array containing raw submitted data.
     */
    public function trigger_webhook( $quote_id, $data ) {
        // Check if Webhooks are active in settings
        if ( ! get_option( 'bqf_pro_webhook_active', 0 ) ) {
            return;
        }

        $webhook_url = get_option( 'bqf_pro_webhook_url', '' );
        if ( empty( $webhook_url ) ) {
            return;
        }

        // Fetch full quote details from DB to ensure we send the final processed data
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';
        $quote = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $quote_id ), ARRAY_A );

        if ( ! $quote ) {
            return;
        }

        // Build the payload
        $payload = wp_json_encode( array(
            'event'   => 'quote_created',
            'lead'    => $quote
        ) );

        // Send POST request asynchronously
        $args = array(
            'body'        => $payload,
            'headers'     => array(
                'Content-Type' => 'application/json',
            ),
            'timeout'     => 15,
            'blocking'    => true, // Required to read the response for logging
        );

        $response = wp_remote_post( $webhook_url, $args );

        // Log the outcome to the timeline if Logger exists
        if ( class_exists( 'BQF_Logger' ) ) {
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                BQF_Logger::log_timeline( $quote_id, "Webhook Failed: {$error_message}" );
            } else {
                $status_code = wp_remote_retrieve_response_code( $response );
                if ( $status_code >= 200 && $status_code < 300 ) {
                    BQF_Logger::log_timeline( $quote_id, "Webhook Sent Successfully (HTTP {$status_code})" );
                } else {
                    BQF_Logger::log_timeline( $quote_id, "Webhook Returned Error (HTTP {$status_code})" );
                }
            }
        }
    }
}
