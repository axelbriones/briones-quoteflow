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
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            product_name varchar(255) NOT NULL,
            product_price varchar(50) NOT NULL,
            name varchar(255) NOT NULL,
            company varchar(255) DEFAULT '' NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(50) NOT NULL,
            message text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            status varchar(20) DEFAULT 'pending' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function insert_quote( $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';

        return $wpdb->insert(
            $table_name,
            array(
                'product_id'    => sanitize_text_field( $data['product_id'] ),
                'product_name'  => sanitize_text_field( $data['product_name'] ),
                'product_price' => sanitize_text_field( $data['product_price'] ),
                'name'          => sanitize_text_field( $data['full_name'] ),
                'company'       => sanitize_text_field( $data['company'] ),
                'email'         => sanitize_email( $data['email'] ),
                'phone'         => sanitize_text_field( $data['phone'] ),
                'message'       => sanitize_textarea_field( $data['message'] ),
                'status'        => 'pending'
            )
        );
    }
}
