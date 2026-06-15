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

        // Catalog Mode / Page Builder Fallbacks
        if ( get_option( 'bqf_catalog_mode', 0 ) ) {
            // Remove mini cart widget
            remove_action( 'wp_footer', 'woocommerce_demo_store' );
            add_action( 'wp_enqueue_scripts', array( $this, 'hide_cart_icons_css' ), 99 );
            add_filter( 'woocommerce_is_purchasable', '__return_false' );

            // Deep Page Builder Interception (Elementor, Divi custom buttons)
            add_action( 'wp_footer', array( $this, 'aggressive_builder_override_js' ), 999 );
        }

        // Builder Compatibility Shortcode
        add_shortcode( 'bqf_quote_button', array( $this, 'render_shortcode_button' ) );
    }

    public function hide_cart_icons_css() {
        $css = '
            .widget_shopping_cart,
            .site-header-cart,
            .cart-contents,
            .footer-cart-contents,
            a.cart-icon,
            .woocommerce-mini-cart,
            .cart-customlocation,
            .elementor-widget-woocommerce-cart,
            .elementor-widget-woocommerce-checkout-page { display: none !important; }

            /* Aggressively hide builder add-to-cart buttons if catalog mode is on */
            form.cart button.single_add_to_cart_button,
            .elementor-add-to-cart,
            .elementor-button.elementor-size-sm.elementor-add-to-cart { display: none !important; }
        ';
        wp_add_inline_style( 'woocommerce-general', $css );
    }

    public function aggressive_builder_override_js() {
        if ( ! is_product() ) {
            return;
        }

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
        $is_variable = $product->is_type( 'variable' ) ? '1' : '0';

        ?>
        <script>
        jQuery(document).ready(function($) {
            // Check if our button is NOT on the page, but a cart form is (meaning a builder overrode our PHP hooks)
            if ( $('.bqf-quote-button').length === 0 && $('form.cart').length > 0 ) {
                var btnHtml = '<div style="margin-top: 15px;"><a href="#" class="bqf-quote-button" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_name="<?php echo esc_attr( $product->get_name() ); ?>" data-product_price="<?php echo esc_attr( $clean_price ); ?>" data-is_variable="<?php echo esc_attr( $is_variable ); ?>"><?php echo esc_html( $button_text ); ?></a></div>';

                // Append it right after the add-to-cart form (which is visually hidden by our CSS above)
                $('form.cart').after(btnHtml);
            }
        });
        </script>
        <?php
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

    public function render_shortcode_button( $atts ) {
        global $product;

        $atts = shortcode_atts( array(
            'product_id' => 0,
        ), $atts, 'bqf_quote_button' );

        $target_product = null;

        if ( ! empty( $atts['product_id'] ) ) {
            $target_product = wc_get_product( intval( $atts['product_id'] ) );
        } elseif ( $product ) {
            $target_product = $product;
        }

        if ( ! $target_product ) {
            return '';
        }

        $button_text = get_option( 'bqf_button_text', 'Request a Quote' );
        $price = $target_product->get_price();
        if ( empty( $price ) ) {
            $price = '0';
        }

        $clean_price = wp_strip_all_tags( html_entity_decode( wc_price( $price ) ) );

        ob_start();
        echo '<div style="margin-top: 15px;"><a href="#" class="bqf-quote-button" data-product_id="' . esc_attr( $target_product->get_id() ) . '" data-product_name="' . esc_attr( $target_product->get_name() ) . '" data-product_price="' . esc_attr( $clean_price ) . '" data-is_variable="' . esc_attr( $target_product->is_type( 'variable' ) ? '1' : '0' ) . '">' . esc_html( $button_text ) . '</a></div>';
        return ob_get_clean();
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
