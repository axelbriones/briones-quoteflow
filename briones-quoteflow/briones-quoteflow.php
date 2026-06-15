<?php
/**
 * Plugin Name: Briones QuoteFlow
 * Description: Replace WooCommerce Add to Cart with an elegant Request a Quote modal, store leads in DB, and send notifications via wp_mail().
 * Version: 1.1.0
 * Author: Briones
 * Text Domain: briones-quoteflow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'BQF_VERSION', '1.1.0' );
define( 'BQF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BQF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once BQF_PLUGIN_DIR . 'includes/class-database.php';
require_once BQF_PLUGIN_DIR . 'includes/class-logger.php';
require_once BQF_PLUGIN_DIR . 'includes/class-admin.php';
require_once BQF_PLUGIN_DIR . 'includes/class-email.php';
require_once BQF_PLUGIN_DIR . 'includes/class-modal.php';
require_once BQF_PLUGIN_DIR . 'includes/class-woocommerce.php';

class Briones_QuoteFlow {

    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
    }

    public function declare_hpos_compatibility() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    public function activate() {
        BQF_Database::create_table();
    }

    public function init() {
        // Check if WooCommerce is active
        if ( class_exists( 'WooCommerce' ) ) {
            new BQF_Admin();
            new BQF_Modal();
            new BQF_WooCommerce();
        } else {
            add_action( 'admin_notices', array( $this, 'wc_missing_notice' ) );
        }
    }

    public function wc_missing_notice() {
        echo '<div class="error"><p>' . esc_html__( 'Briones QuoteFlow requires WooCommerce to be installed and active.', 'briones-quoteflow' ) . '</p></div>';
    }
}

new Briones_QuoteFlow();
