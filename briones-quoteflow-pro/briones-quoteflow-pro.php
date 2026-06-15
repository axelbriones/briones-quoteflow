<?php
/**
 * Plugin Name: Briones QuoteFlow Pro
 * Description: Premium addon for Briones QuoteFlow. Adds Webhooks, Integrations (HubSpot, Zoho), Analytics, and Automations. Requires the Free version.
 * Version: 1.0.0
 * Author: Briones
 * Text Domain: briones-quoteflow-pro
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'BQF_PRO_VERSION', '1.0.0' );
define( 'BQF_PRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BQF_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Helper function to check if Pro is active.
 * Used globally by Free or themes if they need to check for Pro capabilities.
 */
if ( ! function_exists( 'bqf_is_pro' ) ) {
    function bqf_is_pro() {
        return defined( 'BQF_PRO_VERSION' );
    }
}

class Briones_QuoteFlow_Pro {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init_pro' ), 20 ); // Load after Free plugin (usually priority 10)
    }

    public function init_pro() {
        // Check if the Free core is loaded
        if ( ! class_exists( 'Briones_QuoteFlow' ) ) {
            add_action( 'admin_notices', array( $this, 'core_missing_notice' ) );
            return;
        }

        // Load text domain
        load_plugin_textdomain( 'briones-quoteflow-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        // Load the core Pro architecture
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-loader.php';
        BQF_Pro_Loader::init();
    }

    public function core_missing_notice() {
        $class = 'notice notice-error';
        $message = __( 'Briones QuoteFlow Pro requires Briones QuoteFlow Free to be installed and activated. Pro features are currently disabled.', 'briones-quoteflow-pro' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }
}

// Initialize the Pro plugin
Briones_QuoteFlow_Pro::get_instance();
