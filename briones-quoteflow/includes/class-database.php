<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Database {

    public static function create_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bqf_quotes';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT UNSIGNED NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            product_sku VARCHAR(100) DEFAULT '',
            product_price VARCHAR(100) DEFAULT '',
            product_url TEXT,
            product_image TEXT,
            variation_data LONGTEXT,

            name VARCHAR(255) NOT NULL,
            company VARCHAR(255) DEFAULT '',
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(100) DEFAULT '',
            message LONGTEXT,

            status VARCHAR(20) DEFAULT 'new',

            internal_notes LONGTEXT,
            timeline LONGTEXT,
            email_log LONGTEXT,

            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,

            PRIMARY KEY  (id),
            KEY email (email),
            KEY status (status),
            KEY created_at (created_at),
            KEY product_id (product_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function insert_quote( $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';

        $current_time = current_time( 'mysql' );

        // Extract extra product data safely if WooCommerce function is available
        $product_sku = '';
        $product_url = '';
        $product_image = '';

        $pid = intval( $data['product_id'] );
        if ( $pid && function_exists( 'wc_get_product' ) ) {
            $product = wc_get_product( $pid );
            if ( $product ) {
                $product_sku = $product->get_sku();
                $product_url = $product->get_permalink();
                $image_id = $product->get_image_id();
                if ( $image_id ) {
                    $product_image = wp_get_attachment_url( $image_id );
                }
            }
        }

        // Generate initial timeline
        $timeline = array();
        $timeline[] = array(
            'time' => current_time( 'mysql' ),
            'action' => 'Request Created',
            'user' => 'System'
        );

        return $wpdb->insert(
            $table_name,
            array(
                'product_id'    => sanitize_text_field( $data['product_id'] ),
                'product_name'  => sanitize_text_field( $data['product_name'] ),
                'product_sku'   => sanitize_text_field( $product_sku ),
                'product_price' => sanitize_text_field( $data['product_price'] ),
                'product_url'   => esc_url_raw( $product_url ),
                'product_image' => esc_url_raw( $product_image ),
                'variation_data'=> isset( $data['variation_data'] ) ? sanitize_textarea_field( $data['variation_data'] ) : '',
                'name'          => sanitize_text_field( $data['full_name'] ),
                'company'       => sanitize_text_field( $data['company'] ),
                'email'         => sanitize_email( $data['email'] ),
                'phone'         => sanitize_text_field( $data['phone'] ),
                'message'       => sanitize_textarea_field( $data['message'] ),
                'status'        => 'New',
                'timeline'      => wp_json_encode( $timeline ),
                'created_at'    => $current_time,
                'updated_at'    => $current_time
            )
        );
    }
}
