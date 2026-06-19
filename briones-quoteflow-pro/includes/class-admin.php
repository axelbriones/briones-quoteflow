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
            array( $this, 'settings_page' )
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
