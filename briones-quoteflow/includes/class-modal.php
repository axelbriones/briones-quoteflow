<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Modal {

    public function __construct() {
        if ( get_option( 'bqf_enable_modal', 1 ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'wp_footer', array( $this, 'render_modal' ) );
            add_action( 'wp_ajax_bqf_submit_quote', array( $this, 'handle_ajax_submit' ) );
            add_action( 'wp_ajax_nopriv_bqf_submit_quote', array( $this, 'handle_ajax_submit' ) );
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'bqf-frontend-css', BQF_PLUGIN_URL . 'assets/css/frontend.css', array(), BQF_VERSION );
        wp_enqueue_script( 'bqf-frontend-js', BQF_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), BQF_VERSION, true );

        wp_localize_script( 'bqf-frontend-js', 'bqf_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bqf_quote_nonce' )
        ) );
    }

    public function render_modal() {
        if ( is_product() || is_shop() || is_product_category() || is_product_tag() ) {
            include BQF_PLUGIN_DIR . 'templates/quote-modal.php';
        }
    }

    public function handle_ajax_submit() {
        check_ajax_referer( 'bqf_quote_nonce', 'nonce' );

        // Spam Protection: Rate Limiting
        $ip_address = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
        $transient_key = 'bqf_rate_limit_' . md5( $ip_address );
        $attempts = get_transient( $transient_key );

        if ( false !== $attempts && $attempts > 3 ) { // Max 3 requests per IP within the timeframe
            wp_send_json_error( array( 'message' => __( 'Too many requests. Please try again later.', 'briones-quoteflow' ) ) );
        }

        // Spam Protection: Honeypot check
        if ( ! empty( $_POST['bqf_honeypot'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Spam detected.', 'briones-quoteflow' ) ) );
        }

        $required_fields = array( 'product_id', 'product_name', 'product_price', 'full_name', 'email' );
        if ( get_option( 'bqf_field_phone', 1 ) ) {
            $required_fields[] = 'phone';
        }

        foreach ( $required_fields as $field ) {
            if ( empty( $_POST[ $field ] ) ) {
                wp_send_json_error( array( 'message' => __( 'Please fill in all required fields.', 'briones-quoteflow' ) ) );
            }
        }

        $data = array(
            'product_id'    => $_POST['product_id'],
            'product_name'  => $_POST['product_name'],
            'product_price' => $_POST['product_price'],
            'full_name'     => $_POST['full_name'],
            'company'       => isset( $_POST['company'] ) ? $_POST['company'] : '',
            'email'         => $_POST['email'],
            'phone'         => isset( $_POST['phone'] ) ? $_POST['phone'] : '',
            'message'       => isset( $_POST['message'] ) ? $_POST['message'] : ''
        );

        // Save to DB
        $inserted = BQF_Database::insert_quote( $data );

        // Send Email
        $emailed = BQF_Email::send_quote_email( $data );

        // Send Confirmation
        if ( $emailed || $inserted ) {
            BQF_Email::send_customer_confirmation( $data );

            // Increment rate limit attempts
            $attempts = ( false === $attempts ) ? 1 : $attempts + 1;
            set_transient( $transient_key, $attempts, 15 * MINUTE_IN_SECONDS ); // 15 minutes lockout

            $success_message = get_option( 'bqf_success_message', __( 'Your quote request has been sent successfully.', 'briones-quoteflow' ) );
            wp_send_json_success( array( 'message' => $success_message ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'briones-quoteflow' ) ) );
        }
    }
}
