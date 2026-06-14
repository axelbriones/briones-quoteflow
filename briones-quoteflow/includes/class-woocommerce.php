<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_WooCommerce {

    public function __construct() {
        // Disable Add to Cart on single product page
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        add_action( 'woocommerce_single_product_summary', array( $this, 'custom_quote_button' ), 30 );

        // Disable Add to Cart on loop pages
        add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'custom_loop_quote_button' ), 10, 2 );

        // Disable Cart
        add_action( 'template_redirect', array( $this, 'disable_cart_checkout' ) );

        // Hide price if disabled
        if ( ! get_option( 'bqf_enable_price', 1 ) ) {
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
        }
    }

    public function custom_quote_button() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $button_text = get_option( 'bqf_button_text', 'Request a Quote' );

        $price = $product->get_price();
        if ( empty( $price ) ) {
            $price = '0';
        }

        $clean_price = wp_strip_all_tags( html_entity_decode( wc_price( $price ) ) );

        echo '<a href="#" class="bqf-quote-button" data-product_id="' . esc_attr( $product->get_id() ) . '" data-product_name="' . esc_attr( $product->get_name() ) . '" data-product_price="' . esc_attr( $clean_price ) . '">' . esc_html( $button_text ) . '</a>';
    }

    public function custom_loop_quote_button( $button, $product ) {
        $button_text = get_option( 'bqf_button_text', 'Request a Quote' );

        $price = $product->get_price();
        if ( empty( $price ) ) {
            $price = '0';
        }

        $clean_price = wp_strip_all_tags( html_entity_decode( wc_price( $price ) ) );

        return '<a href="#" class="bqf-quote-button" data-product_id="' . esc_attr( $product->get_id() ) . '" data-product_name="' . esc_attr( $product->get_name() ) . '" data-product_price="' . esc_attr( $clean_price ) . '">' . esc_html( $button_text ) . '</a>';
    }

    public function disable_cart_checkout() {
        if ( is_cart() || is_checkout() ) {
            wp_redirect( wc_get_page_permalink( 'shop' ) );
            exit;
        }
    }
}
