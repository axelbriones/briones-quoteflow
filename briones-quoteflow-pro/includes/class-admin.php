<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_pro_menus' ) );
        add_action( 'admin_init', array( $this, 'register_pro_settings' ) );
    }

    public function register_pro_settings() {
        register_setting( 'bqf_pro_settings_group', 'bqf_pro_webhook_active' );
        register_setting( 'bqf_pro_settings_group', 'bqf_pro_webhook_url' );
    }

    public function register_pro_menus() {
        // Main Pro Menu
        add_menu_page(
            __( 'QuoteFlow Pro', 'briones-quoteflow-pro' ),
            __( 'QuoteFlow Pro', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-overview',
            array( $this, 'overview_page' ),
            'dashicons-star-filled',
            57
        );

        // Submenus
        add_submenu_page(
            'bqf-pro-overview',
            __( 'Overview', 'briones-quoteflow-pro' ),
            __( 'Overview', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-overview',
            array( $this, 'overview_page' )
        );

        add_submenu_page(
            'bqf-pro-overview',
            __( 'Integrations', 'briones-quoteflow-pro' ),
            __( 'Integrations', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-integrations',
            array( $this, 'placeholder_page' )
        );

        add_submenu_page(
            'bqf-pro-overview',
            __( 'Automations', 'briones-quoteflow-pro' ),
            __( 'Automations', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-automations',
            array( $this, 'automations_page' )
        );

        add_submenu_page(
            'bqf-pro-overview',
            __( 'Analytics', 'briones-quoteflow-pro' ),
            __( 'Analytics', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-analytics',
            array( $this, 'placeholder_page' )
        );

        add_submenu_page(
            'bqf-pro-overview',
            __( 'Settings', 'briones-quoteflow-pro' ),
            __( 'Settings', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-settings',
            array( $this, 'placeholder_page' )
        );
    }

    public function overview_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Briones QuoteFlow Pro Overview', 'briones-quoteflow-pro' ); ?></h1>
            <div class="notice notice-success inline" style="margin-top:20px;">
                <p><strong><?php esc_html_e( 'System Status', 'briones-quoteflow-pro' ); ?></strong></p>
                <p>&#x2714; <?php esc_html_e( 'Briones QuoteFlow Free: Connected', 'briones-quoteflow-pro' ); ?></p>
                <p>&#x2714; <?php esc_html_e( 'Briones QuoteFlow Pro: Active', 'briones-quoteflow-pro' ); ?></p>
            </div>

            <h2 style="margin-top:30px;"><?php esc_html_e( 'Premium Modules Roadmap', 'briones-quoteflow-pro' ); ?></h2>
            <p><?php esc_html_e( 'The underlying architecture has been established. The following features will be connected to the core system in upcoming releases:', 'briones-quoteflow-pro' ); ?></p>
            <ul style="list-style-type:disc; padding-left:20px;">
                <li>Webhooks & REST API Extensions</li>
                <li>HubSpot & Zoho CRM Integration</li>
                <li>n8n, Slack & Telegram Automations</li>
                <li>Advanced Visual Analytics</li>
                <li>Custom Fields Builder</li>
                <li>PDF Generators & Lead Assignment</li>
            </ul>
        </div>
        <?php
    }

    public function placeholder_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Premium Module Settings', 'briones-quoteflow-pro' ); ?></h1>
            <p><?php esc_html_e( 'This module is structurally loaded and awaiting business logic implementation.', 'briones-quoteflow-pro' ); ?></p>
        </div>
        <?php
    }

    public function automations_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Automations & Webhooks', 'briones-quoteflow-pro' ); ?></h1>
            <p><?php esc_html_e( 'Connect Briones QuoteFlow to external services like Zapier, Make (Integromat), or custom endpoints by sending a JSON payload every time a new quote request is created.', 'briones-quoteflow-pro' ); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_pro_settings_group' ); ?>
                <?php do_settings_sections( 'bqf_pro_settings_group' ); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Enable Webhook', 'briones-quoteflow-pro' ); ?></th>
                        <td>
                            <input type="checkbox" name="bqf_pro_webhook_active" value="1" <?php checked( 1, get_option('bqf_pro_webhook_active', 0), true ); ?> />
                            <?php esc_html_e( 'Send a POST request when a new quote is created.', 'briones-quoteflow-pro' ); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Webhook URL', 'briones-quoteflow-pro' ); ?></th>
                        <td>
                            <input type="url" name="bqf_pro_webhook_url" value="<?php echo esc_url( get_option('bqf_pro_webhook_url', '') ); ?>" class="regular-text" placeholder="https://hooks.zapier.com/..." />
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
