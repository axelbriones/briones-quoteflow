<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_init', array( $this, 'handle_csv_export' ) );
        add_action( 'admin_init', array( $this, 'handle_status_update' ) );
        add_action( 'wp_ajax_bqf_generate_pdf', array( $this, 'generate_pdf_view' ) );
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

        add_submenu_page(
            'quoteflow',
            'Quotes',
            'Quotes',
            'manage_options',
            'quoteflow-quotes',
            array( $this, 'quotes_page' )
        );
    }

    public function register_settings() {
        register_setting( 'bqf_settings_group', 'bqf_notification_email' );
        register_setting( 'bqf_settings_group', 'bqf_success_message' );
        register_setting( 'bqf_settings_group', 'bqf_button_text' );
        register_setting( 'bqf_settings_group', 'bqf_enable_modal' );
        register_setting( 'bqf_settings_group', 'bqf_enable_price' );

        // New features
        register_setting( 'bqf_settings_group', 'bqf_catalog_mode' );
        register_setting( 'bqf_settings_group', 'bqf_field_company' );
        register_setting( 'bqf_settings_group', 'bqf_field_phone' );
        register_setting( 'bqf_settings_group', 'bqf_field_message' );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'QuoteFlow Settings', 'briones-quoteflow' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_settings_group' ); ?>
                <?php do_settings_sections( 'bqf_settings_group' ); ?>

                <h2><?php esc_html_e( 'General Settings', 'briones-quoteflow' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Notification Email', 'briones-quoteflow' ); ?></th>
                        <td><input type="email" name="bqf_notification_email" value="<?php echo esc_attr( get_option('bqf_notification_email', get_option('admin_email')) ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Success Message', 'briones-quoteflow' ); ?></th>
                        <td><input type="text" name="bqf_success_message" value="<?php echo esc_attr( get_option('bqf_success_message', 'Your quote request has been sent successfully.') ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Button Text', 'briones-quoteflow' ); ?></th>
                        <td><input type="text" name="bqf_button_text" value="<?php echo esc_attr( get_option('bqf_button_text', 'Request a Quote') ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Enable Modal', 'briones-quoteflow' ); ?></th>
                        <td><input type="checkbox" name="bqf_enable_modal" value="1" <?php checked( 1, get_option('bqf_enable_modal', 1), true ); ?> /> <?php esc_html_e( 'Check to enable the modal functionality.', 'briones-quoteflow' ); ?></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Enable Product Price', 'briones-quoteflow' ); ?></th>
                        <td><input type="checkbox" name="bqf_enable_price" value="1" <?php checked( 1, get_option('bqf_enable_price', 1), true ); ?> /> <?php esc_html_e( 'Check to keep the product price visible.', 'briones-quoteflow' ); ?></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Catalog Mode', 'briones-quoteflow' ); ?></th>
                        <td><input type="checkbox" name="bqf_catalog_mode" value="1" <?php checked( 1, get_option('bqf_catalog_mode', 0), true ); ?> /> <?php esc_html_e( 'Check to hide cart icons, mini-cart, and prevent checkout access globally.', 'briones-quoteflow' ); ?></td>
                    </tr>
                </table>

                <h2><?php esc_html_e( 'Form Fields Configuration', 'briones-quoteflow' ); ?></h2>
                <p><?php esc_html_e( 'Select which fields should be visible in the quote request form (Name and Email are always required).', 'briones-quoteflow' ); ?></p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Company', 'briones-quoteflow' ); ?></th>
                        <td><input type="checkbox" name="bqf_field_company" value="1" <?php checked( 1, get_option('bqf_field_company', 1), true ); ?> /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Phone', 'briones-quoteflow' ); ?></th>
                        <td><input type="checkbox" name="bqf_field_phone" value="1" <?php checked( 1, get_option('bqf_field_phone', 1), true ); ?> /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Message', 'briones-quoteflow' ); ?></th>
                        <td><input type="checkbox" name="bqf_field_message" value="1" <?php checked( 1, get_option('bqf_field_message', 1), true ); ?> /></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function quotes_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';

        // Metrics
        $total_quotes = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $today_quotes = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = CURDATE()" );
        $month_quotes = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())" );

        $quotes = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 100" );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Quotes Dashboard', 'briones-quoteflow' ); ?></h1>

            <div style="display:flex; gap:20px; margin-bottom: 20px;">
                <div style="background:#fff; padding:15px; border:1px solid #ccc; border-radius:5px; text-align:center; min-width: 150px;">
                    <h3 style="margin:0;"><?php esc_html_e( 'Today', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:24px; margin:10px 0 0 0; font-weight:bold;"><?php echo esc_html( $today_quotes ); ?></p>
                </div>
                <div style="background:#fff; padding:15px; border:1px solid #ccc; border-radius:5px; text-align:center; min-width: 150px;">
                    <h3 style="margin:0;"><?php esc_html_e( 'This Month', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:24px; margin:10px 0 0 0; font-weight:bold;"><?php echo esc_html( $month_quotes ); ?></p>
                </div>
                <div style="background:#fff; padding:15px; border:1px solid #ccc; border-radius:5px; text-align:center; min-width: 150px;">
                    <h3 style="margin:0;"><?php esc_html_e( 'Total', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:24px; margin:10px 0 0 0; font-weight:bold;"><?php echo esc_html( $total_quotes ); ?></p>
                </div>
            </div>

            <form method="post" action="" style="margin-bottom: 20px;">
                <?php wp_nonce_field( 'bqf_export_nonce', 'bqf_export_nonce' ); ?>
                <input type="hidden" name="bqf_export_csv" value="1">
                <?php submit_button( __( 'Export All Quotes to CSV', 'briones-quoteflow' ), 'primary', 'submit', false ); ?>
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Email Logs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $quotes ) : ?>
                        <?php foreach ( $quotes as $quote ) : ?>
                            <tr>
                                <td><?php echo esc_html( $quote->created_at ); ?></td>
                                <td><?php echo esc_html( $quote->product_name ); ?></td>
                                <td><?php echo esc_html( $quote->name ); ?></td>
                                <td><?php echo esc_html( $quote->email ); ?></td>
                                <td>
                                    <?php echo esc_html( $quote->phone ); ?>
                                    <?php if ( ! empty( $quote->phone ) ) :
                                        $clean_phone = preg_replace('/[^0-9+]/', '', $quote->phone);
                                        $wa_message = rawurlencode( "Hello {$quote->name},\n\nWe received your quote request for {$quote->product_name}." );
                                        $wa_url = "https://wa.me/{$clean_phone}?text={$wa_message}";
                                    ?>
                                        <br><a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" style="color: #25D366; text-decoration: none; font-weight: bold; font-size: 12px;">&#x1F4F1; WhatsApp</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" action="">
                                        <?php wp_nonce_field( 'bqf_status_nonce', 'bqf_status_nonce' ); ?>
                                        <input type="hidden" name="bqf_quote_id" value="<?php echo esc_attr( $quote->id ); ?>">
                                        <select name="bqf_new_status" onchange="this.form.submit()">
                                            <option value="New" <?php selected( $quote->status, 'New' ); ?>>New</option>
                                            <option value="Contacted" <?php selected( $quote->status, 'Contacted' ); ?>>Contacted</option>
                                            <option value="Quoted" <?php selected( $quote->status, 'Quoted' ); ?>>Quoted</option>
                                            <option value="Won" <?php selected( $quote->status, 'Won' ); ?>>Won</option>
                                            <option value="Lost" <?php selected( $quote->status, 'Lost' ); ?>>Lost</option>
                                            <option value="pending" <?php selected( $quote->status, 'pending' ); ?>>Pending</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php if ( ! empty( $quote->email_log ) ) : ?>
                                        <span style="font-size: 11px; color: #666;"><?php echo esc_html( $quote->email_log ); ?></span>
                                    <?php else : ?>
                                        <span style="font-size: 11px; color: #aaa;">No logs</span>
                                    <?php endif; ?>
                                    <br>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=bqf_generate_pdf&quote_id=' . $quote->id ), 'bqf_pdf_nonce' ) ); ?>" target="_blank" class="button button-small" style="margin-top:5px;">Print PDF</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7">No quotes found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function handle_status_update() {
        if ( isset( $_POST['bqf_quote_id'] ) && isset( $_POST['bqf_new_status'] ) && isset( $_POST['bqf_status_nonce'] ) && wp_verify_nonce( $_POST['bqf_status_nonce'], 'bqf_status_nonce' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'bqf_quotes';
            $quote_id = intval( $_POST['bqf_quote_id'] );
            $new_status = sanitize_text_field( $_POST['bqf_new_status'] );

            $wpdb->update(
                $table_name,
                array( 'status' => $new_status ),
                array( 'id' => $quote_id ),
                array( '%s' ),
                array( '%d' )
            );

            // Redirect back to same page to prevent re-submission
            wp_redirect( add_query_arg( array( 'page' => 'quoteflow-quotes', 'updated' => 'true' ), admin_url( 'admin.php' ) ) );
            exit;
        }
    }

    public function handle_csv_export() {
        if ( isset( $_POST['bqf_export_csv'] ) && isset( $_POST['bqf_export_nonce'] ) && wp_verify_nonce( $_POST['bqf_export_nonce'], 'bqf_export_nonce' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'bqf_quotes';
            $quotes = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A );

            if ( empty( $quotes ) ) {
                return;
            }

            header( 'Content-Type: text/csv; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=quotes-' . date( 'Y-m-d' ) . '.csv' );

            $output = fopen( 'php://output', 'w' );

            // Add BOM for Excel UTF-8 display
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv( $output, array( 'Date', 'Product', 'Name', 'Email', 'Phone', 'Company', 'Message', 'Status' ) );

            foreach ( $quotes as $quote ) {
                fputcsv( $output, array(
                    $quote['created_at'],
                    $quote['product_name'],
                    $quote['name'],
                    $quote['email'],
                    $quote['phone'],
                    isset($quote['company']) ? $quote['company'] : '',
                    $quote['message'],
                    $quote['status']
                ) );
            }

            fclose( $output );
            exit;
        }
    }

    public function generate_pdf_view() {
        if ( ! current_user_can( 'manage_options' ) || empty( $_GET['quote_id'] ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'bqf_pdf_nonce' ) ) {
            wp_die( 'Unauthorized access.' );
        }

        global $wpdb;
        $quote_id = intval( $_GET['quote_id'] );
        $table_name = $wpdb->prefix . 'bqf_quotes';
        $quote = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $quote_id ) );

        if ( ! $quote ) {
            wp_die( 'Quote not found.' );
        }

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Quote Request #<?php echo esc_html( $quote->id ); ?></title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 40px; color: #333; max-width: 800px; margin: 0 auto; }
                .header { border-bottom: 2px solid #dca54a; padding-bottom: 20px; margin-bottom: 30px; }
                .header h1 { margin: 0; color: #dca54a; }
                .header p { margin: 5px 0 0 0; color: #666; }
                .section { margin-bottom: 30px; }
                .section h2 { font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
                table { width: 100%; border-collapse: collapse; }
                table th, table td { padding: 10px; border: 1px solid #eee; text-align: left; }
                table th { background: #f9f9f9; width: 30%; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="no-print" style="margin-bottom: 20px; text-align: right;">
                <button onclick="window.print();" style="padding: 10px 20px; background: #dca54a; color: #fff; border: none; cursor: pointer; border-radius: 4px;">Print / Save as PDF</button>
            </div>

            <div class="header">
                <h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
                <p>Quote Request #<?php echo str_pad( esc_html( $quote->id ), 6, '0', STR_PAD_LEFT ); ?></p>
                <p>Date: <?php echo esc_html( date( 'F j, Y', strtotime( $quote->created_at ) ) ); ?></p>
            </div>

            <div class="section">
                <h2>Product Details</h2>
                <table>
                    <tr>
                        <th>Product</th>
                        <td><?php echo esc_html( $quote->product_name ); ?></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td><?php echo esc_html( $quote->product_price ); ?></td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Customer Details</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <td><?php echo esc_html( $quote->name ); ?></td>
                    </tr>
                    <?php if ( ! empty( $quote->company ) ) : ?>
                    <tr>
                        <th>Company</th>
                        <td><?php echo esc_html( $quote->company ); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Email</th>
                        <td><?php echo esc_html( $quote->email ); ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo esc_html( $quote->phone ); ?></td>
                    </tr>
                </table>
            </div>

            <?php if ( ! empty( $quote->message ) ) : ?>
            <div class="section">
                <h2>Additional Message</h2>
                <p style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;"><?php echo nl2br( esc_html( $quote->message ) ); ?></p>
            </div>
            <?php endif; ?>

            <div style="margin-top: 50px; text-align: center; color: #999; font-size: 12px;">
                <p>Thank you for your interest.</p>
            </div>

            <script>
                // Auto-trigger print dialog when opened
                window.onload = function() { window.print(); }
            </script>
        </body>
        </html>
        <?php
        exit;
    }
}
