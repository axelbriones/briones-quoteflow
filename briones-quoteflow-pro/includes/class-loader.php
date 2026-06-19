<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_Loader {

    public static function init() {
        self::includes();
        self::instantiate();
    }

    private static function includes() {
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-admin.php';
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-license.php';
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-updater.php';

        // Modules placeholders
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-integrations.php';
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-webhooks.php';
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-analytics.php';
        require_once BQF_PRO_PLUGIN_DIR . 'includes/class-custom-fields.php';
    }

    private static function instantiate() {
        new BQF_Pro_License();
        new BQF_Pro_Updater();
        new BQF_Pro_Admin();

        // Modules
        new BQF_Pro_Integrations();
        new BQF_Pro_Webhooks();
        new BQF_Pro_Analytics();
        new BQF_Pro_Custom_Fields();
    }
}
