<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_menu_page() {
        add_menu_page(
            'QuoteFlow Settings',
            'QuoteFlow',
            'manage_options',
            'quoteflow',
            array( $this, 'settings_page' ),
            'dashicons-email',
            56
        );
    }

    public function register_settings() {
        register_setting( 'bqf_settings_group', 'bqf_notification_email' );
        register_setting( 'bqf_settings_group', 'bqf_success_message' );
        register_setting( 'bqf_settings_group', 'bqf_button_text' );
        register_setting( 'bqf_settings_group', 'bqf_enable_modal' );
        register_setting( 'bqf_settings_group', 'bqf_enable_price' );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>QuoteFlow Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_settings_group' ); ?>
                <?php do_settings_sections( 'bqf_settings_group' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Notification Email</th>
                        <td><input type="email" name="bqf_notification_email" value="<?php echo esc_attr( get_option('bqf_notification_email', get_option('admin_email')) ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Success Message</th>
                        <td><input type="text" name="bqf_success_message" value="<?php echo esc_attr( get_option('bqf_success_message', 'Your quote request has been sent successfully.') ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Button Text</th>
                        <td><input type="text" name="bqf_button_text" value="<?php echo esc_attr( get_option('bqf_button_text', 'Request a Quote') ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable Modal</th>
                        <td><input type="checkbox" name="bqf_enable_modal" value="1" <?php checked( 1, get_option('bqf_enable_modal', 1), true ); ?> /> Check to enable the modal functionality.</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Enable Product Price</th>
                        <td><input type="checkbox" name="bqf_enable_price" value="1" <?php checked( 1, get_option('bqf_enable_price', 1), true ); ?> /> Check to keep the product price visible.</td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
