<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_WooCommerce {

    public function __construct() {
        // Remove standard add to cart buttons but keep variation forms
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

        // We still need the variation form, so we hook a custom template for variables
        add_action( 'woocommerce_single_product_summary', array( $this, 'render_product_form' ), 30 );

        // Disable Add to Cart on loop pages
        add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'custom_loop_quote_button' ), 10, 2 );

        // Disable Cart
        add_action( 'template_redirect', array( $this, 'disable_cart_checkout' ) );

        // Hide price if disabled
        if ( ! get_option( 'bqf_enable_price', 1 ) ) {
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
        }

        // Catalog Mode
        if ( get_option( 'bqf_catalog_mode', 0 ) ) {
            // Remove mini cart widget
            remove_action( 'wp_footer', 'woocommerce_demo_store' );
            add_action( 'wp_enqueue_scripts', array( $this, 'hide_cart_icons_css' ), 99 );
            add_filter( 'woocommerce_is_purchasable', '__return_false' );
        }
    }

    public function hide_cart_icons_css() {
        $css = '
            .widget_shopping_cart,
            .site-header-cart,
            .cart-contents,
            .footer-cart-contents,
            a.cart-icon,
            .woocommerce-mini-cart,
            .cart-customlocation { display: none !important; }
        ';
        wp_add_inline_style( 'woocommerce-general', $css );
    }

    public function render_product_form() {
        global $product;

        if ( ! $product ) {
            return;
        }

        if ( $product->is_type( 'variable' ) ) {
            // Render the variations form but hide the add to cart button within it using CSS
            wp_enqueue_script( 'wc-add-to-cart-variation' );
            echo '<style>.woocommerce-variation-add-to-cart .qty, .woocommerce-variation-add-to-cart .single_add_to_cart_button { display: none !important; }</style>';
            woocommerce_variable_add_to_cart();
            $this->custom_quote_button();
        } else {
            $this->custom_quote_button();
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

        echo '<div style="margin-top: 15px;"><a href="#" class="bqf-quote-button" data-product_id="' . esc_attr( $product->get_id() ) . '" data-product_name="' . esc_attr( $product->get_name() ) . '" data-product_price="' . esc_attr( $clean_price ) . '" data-is_variable="' . esc_attr( $product->is_type( 'variable' ) ? '1' : '0' ) . '">' . esc_html( $button_text ) . '</a></div>';
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
