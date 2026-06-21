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

        register_setting( 'bqf_pro_fields_group', 'bqf_pro_custom_fields' );

        // Integrations Settings
        register_setting( 'bqf_pro_integrations_group', 'bqf_pro_hubspot_active' );
        register_setting( 'bqf_pro_integrations_group', 'bqf_pro_hubspot_token' );
        register_setting( 'bqf_pro_integrations_group', 'bqf_pro_gsheets_active' );
        register_setting( 'bqf_pro_integrations_group', 'bqf_pro_gsheets_url' );

        // License Settings
        register_setting( 'bqf_pro_license_group', 'bqf_pro_license_key' );
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
            __( 'License', 'briones-quoteflow-pro' ),
            __( 'License', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-license',
            array( $this, 'license_page' )
        );

        add_submenu_page(
            'bqf-pro-overview',
            __( 'Integrations', 'briones-quoteflow-pro' ),
            __( 'Integrations', 'briones-quoteflow-pro' ),
            'manage_options',
            'bqf-pro-integrations',
            array( $this, 'integrations_page' )
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
            array( $this, 'settings_page' )
        );
    }

    public function license_page() {
        $license = get_option( 'bqf_pro_license_key' );
        $status  = get_option( 'bqf_pro_license_status' );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'QuoteFlow Pro License', 'briones-quoteflow-pro' ); ?></h1>
            <p><?php esc_html_e( 'Enter your license key to enable automatic updates and premium support.', 'briones-quoteflow-pro' ); ?></p>

            <?php settings_errors( 'bqf_pro_license_notices' ); ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_pro_license_group' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <?php esc_html_e( 'License Key', 'briones-quoteflow-pro' ); ?>
                            </th>
                            <td>
                                <input id="bqf_pro_license_key" name="bqf_pro_license_key" type="password" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
                                <label class="description" for="bqf_pro_license_key"><?php esc_html_e( 'Enter your license key', 'briones-quoteflow-pro' ); ?></label>
                            </td>
                        </tr>
                        <?php if ( false !== $license ) { ?>
                            <tr valign="top">
                                <th scope="row" valign="top">
                                    <?php esc_html_e( 'Activate License', 'briones-quoteflow-pro' ); ?>
                                </th>
                                <td>
                                    <?php if ( $status !== false && $status == 'valid' ) { ?>
                                        <span style="color:green; font-weight:bold;"><?php esc_html_e( 'Active', 'briones-quoteflow-pro' ); ?></span><br><br>
                                        <?php wp_nonce_field( 'bqf_pro_nonce', 'bqf_pro_nonce' ); ?>
                                        <input type="submit" class="button-secondary" name="bqf_pro_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'briones-quoteflow-pro' ); ?>"/>
                                    <?php } else {
                                        wp_nonce_field( 'bqf_pro_nonce', 'bqf_pro_nonce' ); ?>
                                        <input type="submit" class="button-primary" name="bqf_pro_license_activate" value="<?php esc_attr_e( 'Activate License', 'briones-quoteflow-pro' ); ?>"/>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php submit_button( __( 'Save Key', 'briones-quoteflow-pro' ) ); ?>
            </form>
        </div>
        <?php
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

    public function settings_page() {
        wp_enqueue_script( 'jquery-ui-sortable' );
        $fields = get_option( 'bqf_pro_custom_fields', array() );
        if ( ! is_array( $fields ) ) {
            $fields = array();
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'QuoteFlow Pro Settings', 'briones-quoteflow-pro' ); ?></h1>

            <h2 class="title"><?php esc_html_e( 'Custom Fields Builder', 'briones-quoteflow-pro' ); ?></h2>
            <p><?php esc_html_e( 'Add dynamic custom fields to your quote request modal. These will automatically appear in the form and be tracked in your CRM.', 'briones-quoteflow-pro' ); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_pro_fields_group' ); ?>

                <table class="wp-list-table widefat fixed striped" style="max-width: 800px; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th><?php esc_html_e( 'Field Label', 'briones-quoteflow-pro' ); ?></th>
                            <th><?php esc_html_e( 'Field Type', 'briones-quoteflow-pro' ); ?></th>
                            <th><?php esc_html_e( 'Required', 'briones-quoteflow-pro' ); ?></th>
                            <th style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="bqf-pro-fields-repeater">
                        <?php
                        $counter = 0;
                        foreach ( $fields as $field ) :
                            $label = isset($field['label']) ? $field['label'] : '';
                            $type  = isset($field['type']) ? $field['type'] : 'text';
                            $req   = isset($field['required']) ? $field['required'] : '0';
                        ?>
                        <tr class="bqf-field-row">
                            <td style="cursor: move; font-size: 20px; text-align: center;">&#x2195;</td>
                            <td>
                                <input type="text" name="bqf_pro_custom_fields[<?php echo $counter; ?>][label]" value="<?php echo esc_attr( $label ); ?>" class="regular-text" placeholder="e.g. Desired Delivery Date" required />
                            </td>
                            <td>
                                <select name="bqf_pro_custom_fields[<?php echo $counter; ?>][type]">
                                    <option value="text" <?php selected( $type, 'text' ); ?>>Text</option>
                                    <option value="number" <?php selected( $type, 'number' ); ?>>Number</option>
                                    <option value="date" <?php selected( $type, 'date' ); ?>>Date</option>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" name="bqf_pro_custom_fields[<?php echo $counter; ?>][required]" value="1" <?php checked( $req, '1' ); ?> />
                            </td>
                            <td>
                                <button type="button" class="button bqf-remove-field" style="color:#d63638; border-color:#d63638;">&times; Remove</button>
                            </td>
                        </tr>
                        <?php $counter++; endforeach; ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" id="bqf-add-field" class="button button-secondary">+ <?php esc_html_e( 'Add Custom Field', 'briones-quoteflow-pro' ); ?></button>
                </p>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($){
            var fieldCounter = <?php echo max( 1, $counter ); ?>;

            $('#bqf-pro-fields-repeater').sortable({
                axis: 'y',
                cursor: 'move'
            });

            $('#bqf-add-field').on('click', function(e){
                e.preventDefault();
                var html = '<tr class="bqf-field-row">' +
                    '<td style="cursor: move; font-size: 20px; text-align: center;">&#x2195;</td>' +
                    '<td><input type="text" name="bqf_pro_custom_fields[' + fieldCounter + '][label]" value="" class="regular-text" placeholder="e.g. Quantity" required /></td>' +
                    '<td>' +
                        '<select name="bqf_pro_custom_fields[' + fieldCounter + '][type]">' +
                            '<option value="text">Text</option>' +
                            '<option value="number">Number</option>' +
                            '<option value="date">Date</option>' +
                        '</select>' +
                    '</td>' +
                    '<td><input type="checkbox" name="bqf_pro_custom_fields[' + fieldCounter + '][required]" value="1" /></td>' +
                    '<td><button type="button" class="button bqf-remove-field" style="color:#d63638; border-color:#d63638;">&times; Remove</button></td>' +
                '</tr>';

                $('#bqf-pro-fields-repeater').append(html);
                fieldCounter++;
            });

            $(document).on('click', '.bqf-remove-field', function(e){
                e.preventDefault();
                if(confirm('<?php esc_attr_e( 'Remove this field?', 'briones-quoteflow-pro' ); ?>')){
                    $(this).closest('tr').remove();
                }
            });
        });
        </script>
        <?php
    }

    public function integrations_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Integrations', 'briones-quoteflow-pro' ); ?></h1>
            <p><?php esc_html_e( 'Connect Briones QuoteFlow natively with your favorite CRM and Spreadsheet tools.', 'briones-quoteflow-pro' ); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_pro_integrations_group' ); ?>
                <?php do_settings_sections( 'bqf_pro_integrations_group' ); ?>

                <h2 class="title" style="border-bottom: 1px solid #ccc; padding-bottom: 10px;"><img src="https://www.hubspot.com/hubfs/assets/hubspot.com/style-guide/brand-guidelines/guidelines_the-sprocket.svg" width="20" style="vertical-align:middle;"> <?php esc_html_e( 'HubSpot CRM', 'briones-quoteflow-pro' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Enable HubSpot', 'briones-quoteflow-pro' ); ?></th>
                        <td>
                            <input type="checkbox" name="bqf_pro_hubspot_active" value="1" <?php checked( 1, get_option('bqf_pro_hubspot_active', 0), true ); ?> />
                            <?php esc_html_e( 'Automatically send new quote requests to HubSpot as Contacts.', 'briones-quoteflow-pro' ); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Private App Access Token', 'briones-quoteflow-pro' ); ?></th>
                        <td>
                            <input type="password" name="bqf_pro_hubspot_token" value="<?php echo esc_attr( get_option('bqf_pro_hubspot_token', '') ); ?>" class="regular-text" placeholder="pat-na1-..." />
                            <p class="description"><?php esc_html_e( 'Create a Private App in HubSpot with crm.objects.contacts.write permissions.', 'briones-quoteflow-pro' ); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title" style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-top: 40px;"><img src="https://upload.wikimedia.org/wikipedia/commons/a/ab/Google_Sheets_Logo_%282020%29.svg" width="16" style="vertical-align:middle;"> <?php esc_html_e( 'Google Sheets', 'briones-quoteflow-pro' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Enable Google Sheets', 'briones-quoteflow-pro' ); ?></th>
                        <td>
                            <input type="checkbox" name="bqf_pro_gsheets_active" value="1" <?php checked( 1, get_option('bqf_pro_gsheets_active', 0), true ); ?> />
                            <?php esc_html_e( 'Append new quote requests as rows in a Google Sheet.', 'briones-quoteflow-pro' ); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Google App Script URL', 'briones-quoteflow-pro' ); ?></th>
                        <td>
                            <input type="url" name="bqf_pro_gsheets_url" value="<?php echo esc_url( get_option('bqf_pro_gsheets_url', '') ); ?>" class="regular-text" placeholder="https://script.google.com/macros/s/.../exec" />
                            <p class="description"><?php esc_html_e( 'Deploy an App Script with doPost(e) bound to your sheet.', 'briones-quoteflow-pro' ); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title" style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-top: 40px; color:#aaa;"><?php esc_html_e( 'Zoho CRM (Coming Soon)', 'briones-quoteflow-pro' ); ?></h2>
                <p style="color:#aaa;"><em><?php esc_html_e( 'Native Zoho integration requires complex OAuth2 flows which are currently being developed for a future update. Please use the Webhooks module to connect to Zoho via Zapier in the meantime.', 'briones-quoteflow-pro' ); ?></em></p>

                <p style="margin-top: 30px;">
                    <?php submit_button(); ?>
                </p>
            </form>
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
