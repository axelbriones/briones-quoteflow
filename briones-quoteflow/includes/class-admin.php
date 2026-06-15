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
        add_action( 'admin_init', array( $this, 'handle_add_note' ) );
        add_action( 'admin_init', array( $this, 'handle_delete_quote' ) );
        add_action( 'admin_init', array( $this, 'handle_test_email' ) );
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

        // Visual and Redirect settings
        register_setting( 'bqf_settings_group', 'bqf_btn_color_primary' );
        register_setting( 'bqf_settings_group', 'bqf_btn_color_hover' );
        register_setting( 'bqf_settings_group', 'bqf_btn_border_radius' );
        register_setting( 'bqf_settings_group', 'bqf_redirect_url' );

        // Email Templates
        register_setting( 'bqf_settings_group', 'bqf_email_admin_subject' );
        register_setting( 'bqf_settings_group', 'bqf_email_customer_subject' );
        register_setting( 'bqf_settings_group', 'bqf_email_customer_body' );
    }

    public function settings_page() {
        // Enqueue color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'QuoteFlow Settings', 'briones-quoteflow' ); ?></h1>
            <?php settings_errors( 'bqf_messages' ); ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=quoteflow&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'briones-quoteflow' ); ?></a>
                <a href="?page=quoteflow&tab=fields" class="nav-tab <?php echo $active_tab == 'fields' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Fields', 'briones-quoteflow' ); ?></a>
                <a href="?page=quoteflow&tab=design" class="nav-tab <?php echo $active_tab == 'design' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Design', 'briones-quoteflow' ); ?></a>
                <a href="?page=quoteflow&tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Email Templates', 'briones-quoteflow' ); ?></a>
            </h2>

            <form method="post" action="options.php">
                <?php settings_fields( 'bqf_settings_group' ); ?>

                <?php if ( $active_tab == 'general' ) : ?>
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
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Thank You Page URL', 'briones-quoteflow' ); ?></th>
                        <td>
                            <input type="url" name="bqf_redirect_url" value="<?php echo esc_url( get_option('bqf_redirect_url', '') ); ?>" class="regular-text" placeholder="https://..." />
                            <p class="description"><?php esc_html_e( 'Leave empty to just show the success message in the modal.', 'briones-quoteflow' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php endif; ?>

                <?php if ( $active_tab == 'design' ) : ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Primary Button Color', 'briones-quoteflow' ); ?></th>
                        <td><input type="text" name="bqf_btn_color_primary" value="<?php echo esc_attr( get_option('bqf_btn_color_primary', '#dca54a') ); ?>" class="bqf-color-picker" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Hover Button Color', 'briones-quoteflow' ); ?></th>
                        <td><input type="text" name="bqf_btn_color_hover" value="<?php echo esc_attr( get_option('bqf_btn_color_hover', '#f27305') ); ?>" class="bqf-color-picker" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Button Border Radius', 'briones-quoteflow' ); ?></th>
                        <td>
                            <input type="number" name="bqf_btn_border_radius" value="<?php echo esc_attr( get_option('bqf_btn_border_radius', '50') ); ?>" class="small-text" /> px
                        </td>
                    </tr>
                </table>
                <?php endif; ?>

                <?php if ( $active_tab == 'fields' ) : ?>
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
                <?php endif; ?>

                <?php if ( $active_tab == 'email' ) : ?>
                <p><?php esc_html_e( 'Available Variables: {product_name}, {customer_name}, {product_price}', 'briones-quoteflow' ); ?></p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Admin Notification Subject', 'briones-quoteflow' ); ?></th>
                        <td>
                            <input type="text" name="bqf_email_admin_subject" value="<?php echo esc_attr( get_option('bqf_email_admin_subject', 'New Quote Request - {product_name}') ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Customer Confirmation Subject', 'briones-quoteflow' ); ?></th>
                        <td>
                            <input type="text" name="bqf_email_customer_subject" value="<?php echo esc_attr( get_option('bqf_email_customer_subject', 'We have received your quote request') ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Customer Confirmation Body', 'briones-quoteflow' ); ?></th>
                        <td>
                            <textarea name="bqf_email_customer_body" rows="5" class="large-text"><?php
                                $default_body = "Hello {customer_name},\n\nThank you for contacting us.\n\nProduct: {product_name}\n\nOur team will review your request and contact you shortly.";
                                echo esc_textarea( get_option('bqf_email_customer_body', $default_body) );
                            ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'Test Email Configuration', 'briones-quoteflow' ); ?></th>
                        <td>
                            <button type="submit" name="bqf_send_test_email" value="1" class="button button-secondary"><?php esc_html_e( 'Send Test Email', 'briones-quoteflow' ); ?></button>
                            <p class="description"><?php esc_html_e( 'Click to send a test email to the Admin Notification Email address to verify your SMTP settings.', 'briones-quoteflow' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php endif; ?>

                <?php submit_button(); ?>
            </form>
        </div>
        <script>
            jQuery(document).ready(function($){
                if (typeof $.fn.wpColorPicker !== 'undefined') {
                    $('.bqf-color-picker').wpColorPicker();
                }
            });
        </script>
        <?php
    }

    public function quotes_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';

        // Router for Single View vs List View
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'view' && ! empty( $_GET['id'] ) ) {
            $this->single_quote_view( intval( $_GET['id'] ) );
            return;
        }

        // Advanced Metrics
        $total_quotes = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $won_quotes = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE status = %s", 'Won' ) );
        $lost_quotes = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE status = %s", 'Lost' ) );
        $new_quotes = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE status = %s", 'New' ) );

        $conversion_rate = $total_quotes > 0 ? round( ($won_quotes / $total_quotes) * 100, 1 ) : 0;

        $top_products = $wpdb->get_results( "SELECT product_name, COUNT(*) as count FROM $table_name GROUP BY product_id ORDER BY count DESC LIMIT 5" );

        // Search & Pagination Logic
        $per_page = 20;
        $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset = ( $current_page - 1 ) * $per_page;
        $search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

        $where_clause = "";
        if ( ! empty( $search_term ) ) {
            $like = '%' . $wpdb->esc_like( $search_term ) . '%';
            $where_clause = $wpdb->prepare( "WHERE product_name LIKE %s OR name LIKE %s OR email LIKE %s", $like, $like, $like );
        }

        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $where_clause" );
        $total_pages = ceil( $total_items / $per_page );

        $quotes = $wpdb->get_results( "SELECT id, reference_id, product_name, name, email, phone, status, created_at, email_log FROM $table_name $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset" );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Quotes Dashboard', 'briones-quoteflow' ); ?></h1>

            <!-- Professional Metrics Row -->
            <div style="display:flex; gap:15px; margin-bottom: 20px; flex-wrap:wrap;">
                <div style="background:#fff; padding:15px; border:1px solid #ccd0d4; border-radius:4px; text-align:center; flex:1; min-width:120px;">
                    <h3 style="margin:0; font-size:14px; color:#646970;"><?php esc_html_e( 'Total Requests', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:28px; margin:5px 0 0 0; font-weight:600; color:#1d2327;"><?php echo esc_html( $total_quotes ); ?></p>
                </div>
                <div style="background:#fff; padding:15px; border:1px solid #ccd0d4; border-radius:4px; text-align:center; flex:1; min-width:120px; border-top: 3px solid #2271b1;">
                    <h3 style="margin:0; font-size:14px; color:#646970;"><?php esc_html_e( 'New Requests', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:28px; margin:5px 0 0 0; font-weight:600; color:#1d2327;"><?php echo esc_html( $new_quotes ); ?></p>
                </div>
                <div style="background:#fff; padding:15px; border:1px solid #ccd0d4; border-radius:4px; text-align:center; flex:1; min-width:120px; border-top: 3px solid #00a32a;">
                    <h3 style="margin:0; font-size:14px; color:#646970;"><?php esc_html_e( 'Won Deals', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:28px; margin:5px 0 0 0; font-weight:600; color:#1d2327;"><?php echo esc_html( $won_quotes ); ?></p>
                </div>
                <div style="background:#fff; padding:15px; border:1px solid #ccd0d4; border-radius:4px; text-align:center; flex:1; min-width:120px; border-top: 3px solid #d63638;">
                    <h3 style="margin:0; font-size:14px; color:#646970;"><?php esc_html_e( 'Lost Deals', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:28px; margin:5px 0 0 0; font-weight:600; color:#1d2327;"><?php echo esc_html( $lost_quotes ); ?></p>
                </div>
                <div style="background:#fff; padding:15px; border:1px solid #ccd0d4; border-radius:4px; text-align:center; flex:1; min-width:120px; border-top: 3px solid #dca54a;">
                    <h3 style="margin:0; font-size:14px; color:#646970;"><?php esc_html_e( 'Conversion Rate', 'briones-quoteflow' ); ?></h3>
                    <p style="font-size:28px; margin:5px 0 0 0; font-weight:600; color:#1d2327;"><?php echo esc_html( $conversion_rate ); ?>%</p>
                </div>
            </div>

            <!-- Top Products -->
            <div style="background:#fff; padding:15px; border:1px solid #ccd0d4; border-radius:4px; margin-bottom: 20px;">
                <h3 style="margin-top:0;"><?php esc_html_e( 'Top Requested Products', 'briones-quoteflow' ); ?></h3>
                <?php if ( $top_products ) : ?>
                    <ul style="margin-bottom:0;">
                        <?php foreach ( $top_products as $tp ) : ?>
                            <li><strong><?php echo esc_html( $tp->count ); ?></strong> - <?php echo esc_html( $tp->product_name ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?php esc_html_e( 'No data yet.', 'briones-quoteflow' ); ?></p>
                <?php endif; ?>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px;">
                <form method="post" action="" style="display:inline-block;">
                    <?php wp_nonce_field( 'bqf_export_nonce', 'bqf_export_nonce' ); ?>
                    <input type="hidden" name="bqf_export_csv" value="1">
                    <?php submit_button( __( 'Export All Quotes to CSV', 'briones-quoteflow' ), 'primary', 'submit', false ); ?>
                </form>

                <form method="get" action="">
                    <input type="hidden" name="page" value="quoteflow-quotes" />
                    <p class="search-box" style="margin:0;">
                        <label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search Requests:', 'briones-quoteflow' ); ?></label>
                        <input type="search" id="post-search-input" name="s" value="<?php echo esc_attr( $search_term ); ?>">
                        <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search Requests', 'briones-quoteflow' ); ?>">
                    </p>
                </form>
            </div>

            <p><em><?php esc_html_e( 'Click on the Product Name to view full Request details.', 'briones-quoteflow' ); ?></em></p>

            <div class="tablenav top">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php printf( _n( '%s item', '%s items', $total_items, 'briones-quoteflow' ), number_format_i18n( $total_items ) ); ?></span>
                    <?php if ( $total_pages > 1 ) : ?>
                        <span class="pagination-links">
                            <?php
                            echo paginate_links( array(
                                'base'      => add_query_arg( 'paged', '%#%' ),
                                'format'    => '',
                                'prev_text' => __( '&laquo;', 'briones-quoteflow' ),
                                'next_text' => __( '&raquo;', 'briones-quoteflow' ),
                                'total'     => $total_pages,
                                'current'   => $current_page
                            ) );
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Reference', 'briones-quoteflow' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'briones-quoteflow' ); ?></th>
                        <th><?php esc_html_e( 'Product', 'briones-quoteflow' ); ?></th>
                        <th><?php esc_html_e( 'Name', 'briones-quoteflow' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'briones-quoteflow' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'briones-quoteflow' ); ?></th>
                        <th><?php esc_html_e( 'Email Logs', 'briones-quoteflow' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $quotes ) : ?>
                        <?php foreach ( $quotes as $quote ) : ?>
                            <tr>
                                <td>
                                    <strong><a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflow-quotes&action=view&id=' . $quote->id ) ); ?>"><?php echo esc_html( $quote->reference_id ); ?></a></strong>
                                </td>
                                <td><?php echo esc_html( date( 'M j, Y H:i', strtotime( $quote->created_at ) ) ); ?></td>
                                <td><?php echo esc_html( $quote->product_name ); ?></td>
                                <td><?php echo esc_html( $quote->name ); ?></td>
                                <td>
                                    <a href="mailto:<?php echo esc_attr( $quote->email ); ?>"><?php echo esc_html( $quote->email ); ?></a>
                                    <?php if ( ! empty( $quote->phone ) ) :
                                        $clean_phone = preg_replace('/[^0-9+]/', '', $quote->phone);
                                        $wa_message = rawurlencode( "Hello {$quote->name},\n\nWe are following up on your quote request {$quote->reference_id} for {$quote->product_name}." );
                                        $wa_url = "https://wa.me/{$clean_phone}?text={$wa_message}";
                                    ?>
                                        <br><a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" style="color: #25D366; text-decoration: none; font-weight: bold; font-size: 12px;">&#x1F4F1; WhatsApp (<?php echo esc_html( $quote->phone ); ?>)</a>
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
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=bqf_generate_pdf&quote_id=' . $quote->id ), 'bqf_pdf_nonce' ) ); ?>" target="_blank" class="button button-small" style="margin-top:5px;"><?php esc_html_e( 'Print PDF', 'briones-quoteflow' ); ?></a>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=quoteflow-quotes&action=delete&quote_id=' . $quote->id ), 'bqf_delete_nonce' ) ); ?>" class="button button-small" style="margin-top:5px; color:#d63638; border-color:#d63638;" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this request? This action cannot be undone.', 'briones-quoteflow' ); ?>');"><?php esc_html_e( 'Delete', 'briones-quoteflow' ); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 30px;">
                                <?php esc_html_e( 'No quote requests yet.', 'briones-quoteflow' ); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php printf( _n( '%s item', '%s items', $total_items, 'briones-quoteflow' ), number_format_i18n( $total_items ) ); ?></span>
                    <?php if ( $total_pages > 1 ) : ?>
                        <span class="pagination-links">
                            <?php
                            echo paginate_links( array(
                                'base'      => add_query_arg( 'paged', '%#%' ),
                                'format'    => '',
                                'prev_text' => __( '&laquo;', 'briones-quoteflow' ),
                                'next_text' => __( '&raquo;', 'briones-quoteflow' ),
                                'total'     => $total_pages,
                                'current'   => $current_page
                            ) );
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php
    }

    public function single_quote_view( $quote_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bqf_quotes';
        $quote = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $quote_id ) );

        if ( ! $quote ) {
            echo '<div class="wrap"><h1>Request Not Found</h1></div>';
            return;
        }

        $timeline = json_decode( $quote->timeline, true );
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Quote Request ', 'briones-quoteflow' ); ?><?php echo esc_html( $quote->reference_id ); ?></h1>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=quoteflow-quotes' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Back to Requests', 'briones-quoteflow' ); ?></a>
            <hr class="wp-header-end">

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">

                    <div id="post-body-content">

                        <!-- Product Info -->
                        <div class="postbox">
                            <h2 class="hndle"><span><?php esc_html_e( 'Product Details', 'briones-quoteflow' ); ?></span></h2>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th><?php esc_html_e( 'Product', 'briones-quoteflow' ); ?></th>
                                        <td>
                                            <?php echo esc_html( $quote->product_name ); ?>
                                            <?php if ( ! empty( $quote->product_sku ) ) echo '<br><small>SKU: ' . esc_html( $quote->product_sku ) . '</small>'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php esc_html_e( 'Price', 'briones-quoteflow' ); ?></th>
                                        <td><?php echo esc_html( $quote->product_price ); ?></td>
                                    </tr>
                                    <?php if ( ! empty( $quote->variation_data ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Variations', 'briones-quoteflow' ); ?></th>
                                        <td><?php echo nl2br( esc_html( $quote->variation_data ) ); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $quote->product_url ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Link', 'briones-quoteflow' ); ?></th>
                                        <td><a href="<?php echo esc_url( $quote->product_url ); ?>" target="_blank"><?php esc_html_e( 'View Product', 'briones-quoteflow' ); ?></a></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="postbox">
                            <h2 class="hndle"><span><?php esc_html_e( 'Customer Information', 'briones-quoteflow' ); ?></span></h2>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th><?php esc_html_e( 'Name', 'briones-quoteflow' ); ?></th>
                                        <td><?php echo esc_html( $quote->name ); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php esc_html_e( 'Email', 'briones-quoteflow' ); ?></th>
                                        <td><a href="mailto:<?php echo esc_attr( $quote->email ); ?>"><?php echo esc_html( $quote->email ); ?></a></td>
                                    </tr>
                                    <tr>
                                        <th><?php esc_html_e( 'Phone', 'briones-quoteflow' ); ?></th>
                                        <td>
                                            <?php echo esc_html( $quote->phone ); ?>
                                            <?php if ( ! empty( $quote->phone ) ) :
                                                $clean_phone = preg_replace('/[^0-9+]/', '', $quote->phone);
                                                $wa_message = rawurlencode( "Hello {$quote->name},\n\nWe received your quote request for {$quote->product_name}." );
                                                $wa_url = "https://wa.me/{$clean_phone}?text={$wa_message}";
                                            ?>
                                                <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" style="margin-left:10px; color: #25D366; text-decoration: none; font-weight: bold;">&#x1F4F1; WhatsApp</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if ( ! empty( $quote->company ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Company', 'briones-quoteflow' ); ?></th>
                                        <td><?php echo esc_html( $quote->company ); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $quote->message ) ) : ?>
                                    <tr>
                                        <th><?php esc_html_e( 'Message', 'briones-quoteflow' ); ?></th>
                                        <td><div style="background:#f9f9f9; padding:10px; border:1px solid #ddd;"><?php echo nl2br( esc_html( $quote->message ) ); ?></div></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>

                        <!-- Internal Notes -->
                        <div class="postbox">
                            <h2 class="hndle"><span><?php esc_html_e( 'Internal Notes', 'briones-quoteflow' ); ?></span></h2>
                            <div class="inside">
                                <?php if ( ! empty( $quote->internal_notes ) ) : ?>
                                    <div style="background:#fffcf5; padding:15px; border-left:4px solid #dca54a; margin-bottom:15px;">
                                        <?php echo nl2br( esc_html( $quote->internal_notes ) ); ?>
                                    </div>
                                <?php endif; ?>
                                <form method="post" action="">
                                    <?php wp_nonce_field( 'bqf_note_nonce', 'bqf_note_nonce' ); ?>
                                    <input type="hidden" name="bqf_quote_id" value="<?php echo esc_attr( $quote->id ); ?>">
                                    <textarea name="bqf_internal_note" rows="3" style="width:100%;" placeholder="<?php esc_attr_e( 'Add a private note about this request...', 'briones-quoteflow' ); ?>"></textarea>
                                    <p><button type="submit" class="button"><?php esc_html_e( 'Add Note', 'briones-quoteflow' ); ?></button></p>
                                </form>
                            </div>
                        </div>

                    </div> <!-- /post-body-content -->

                    <!-- Sidebar -->
                    <div id="postbox-container-1" class="postbox-container">

                        <!-- Actions -->
                        <div class="postbox">
                            <h2 class="hndle"><span><?php esc_html_e( 'Actions', 'briones-quoteflow' ); ?></span></h2>
                            <div class="inside">
                                <form method="post" action="">
                                    <?php wp_nonce_field( 'bqf_status_nonce', 'bqf_status_nonce' ); ?>
                                    <input type="hidden" name="bqf_quote_id" value="<?php echo esc_attr( $quote->id ); ?>">
                                    <p><strong><?php esc_html_e( 'Status:', 'briones-quoteflow' ); ?></strong></p>
                                    <select name="bqf_new_status" style="width:100%; margin-bottom:10px;">
                                        <option value="New" <?php selected( $quote->status, 'New' ); ?>>New</option>
                                        <option value="Contacted" <?php selected( $quote->status, 'Contacted' ); ?>>Contacted</option>
                                        <option value="Quoted" <?php selected( $quote->status, 'Quoted' ); ?>>Quoted</option>
                                        <option value="Won" <?php selected( $quote->status, 'Won' ); ?>>Won</option>
                                        <option value="Lost" <?php selected( $quote->status, 'Lost' ); ?>>Lost</option>
                                    </select>
                                    <button type="submit" class="button button-primary" style="width:100%; text-align:center;"><?php esc_html_e( 'Update Status', 'briones-quoteflow' ); ?></button>
                                </form>
                                <hr>
                                <p style="text-align:center;">
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=bqf_generate_pdf&quote_id=' . $quote->id ), 'bqf_pdf_nonce' ) ); ?>" target="_blank" class="button" style="width:100%; text-align:center;"><?php esc_html_e( 'Print PDF', 'briones-quoteflow' ); ?></a>
                                </p>
                                <hr>
                                <p style="text-align:center;">
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=quoteflow-quotes&action=delete&quote_id=' . $quote->id ), 'bqf_delete_nonce' ) ); ?>" class="button" style="width:100%; text-align:center; color:#d63638; border-color:#d63638;" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this request? This action cannot be undone.', 'briones-quoteflow' ); ?>');"><?php esc_html_e( 'Delete Request', 'briones-quoteflow' ); ?></a>
                                </p>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="postbox">
                            <h2 class="hndle"><span><?php esc_html_e( 'Timeline', 'briones-quoteflow' ); ?></span></h2>
                            <div class="inside">
                                <?php if ( ! empty( $timeline ) && is_array( $timeline ) ) : ?>
                                    <ul style="margin:0; padding:0; list-style:none;">
                                        <?php foreach ( array_reverse( $timeline ) as $event ) : ?>
                                            <li style="margin-bottom:15px; padding-left:10px; border-left:2px solid #ccc;">
                                                <small style="color:#999;"><?php echo esc_html( date( 'M j, Y H:i', strtotime( $event['time'] ) ) ); ?></small><br>
                                                <strong><?php echo esc_html( $event['action'] ); ?></strong><br>
                                                <small><?php esc_html_e( 'by', 'briones-quoteflow' ); ?> <?php echo esc_html( $event['user'] ); ?></small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p><?php esc_html_e( 'No activity yet.', 'briones-quoteflow' ); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div> <!-- /postbox-container-1 -->

                </div> <!-- /post-body -->
            </div> <!-- /poststuff -->
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
                array( 'status' => $new_status, 'updated_at' => current_time( 'mysql' ) ),
                array( 'id' => $quote_id ),
                array( '%s', '%s' ),
                array( '%d' )
            );

            if ( class_exists( 'BQF_Logger' ) ) {
                BQF_Logger::log_timeline( $quote_id, "Status Changed to: {$new_status}" );
            }

            // Redirect back to single view if that's where they came from
            if ( isset( $_GET['action'] ) && $_GET['action'] === 'view' ) {
                wp_redirect( add_query_arg( array( 'page' => 'quoteflow-quotes', 'action' => 'view', 'id' => $quote_id, 'updated' => 'true' ), admin_url( 'admin.php' ) ) );
            } else {
                wp_redirect( add_query_arg( array( 'page' => 'quoteflow-quotes', 'updated' => 'true' ), admin_url( 'admin.php' ) ) );
            }
            exit;
        }
    }

    public function handle_test_email() {
        if ( isset( $_POST['bqf_send_test_email'] ) && isset( $_POST['option_page'] ) && $_POST['option_page'] === 'bqf_settings_group' ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $to = get_option( 'bqf_notification_email', get_option( 'admin_email' ) );
            $subject = __( 'Briones QuoteFlow - Test Email', 'briones-quoteflow' );
            $message = '<div style="background:#f5f5f5; padding:30px; font-family:Helvetica,Arial,sans-serif;">';
            $message .= '<div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05);">';
            $message .= '<div style="background:#25D366; padding:20px; color:#ffffff; text-align:center;">';
            $message .= '<h2 style="margin:0;">' . esc_html__( 'Test Email Successful', 'briones-quoteflow' ) . '</h2>';
            $message .= '</div>';
            $message .= '<div style="padding:30px; color:#333333; line-height:1.6;">';
            $message .= '<p>' . esc_html__( 'Hello,', 'briones-quoteflow' ) . '</p>';
            $message .= '<p>' . esc_html__( 'If you are reading this email, it means that your WordPress SMTP settings are correctly configured and Briones QuoteFlow can send HTML emails without issues.', 'briones-quoteflow' ) . '</p>';
            $message .= '</div>';
            $message .= '</div>';
            $message .= '</div>';

            $from_email = get_option( 'admin_email' );
            $from_name = get_bloginfo( 'name' );

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . $from_name . ' <' . $from_email . '>'
            );

            $sent = wp_mail( $to, $subject, $message, $headers );

            if ( $sent ) {
                add_settings_error( 'bqf_messages', 'bqf_test_email_success', __( 'Test email sent successfully! Please check your inbox.', 'briones-quoteflow' ), 'updated' );
            } else {
                add_settings_error( 'bqf_messages', 'bqf_test_email_error', __( 'Failed to send test email. Please check your WordPress SMTP configuration.', 'briones-quoteflow' ), 'error' );
            }
        }
    }

    public function handle_add_note() {
        if ( isset( $_POST['bqf_quote_id'] ) && isset( $_POST['bqf_internal_note'] ) && isset( $_POST['bqf_note_nonce'] ) && wp_verify_nonce( $_POST['bqf_note_nonce'], 'bqf_note_nonce' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'bqf_quotes';
            $quote_id = intval( $_POST['bqf_quote_id'] );
            $new_note = sanitize_textarea_field( $_POST['bqf_internal_note'] );

            if ( empty( $new_note ) ) {
                return;
            }

            $existing = $wpdb->get_var( $wpdb->prepare( "SELECT internal_notes FROM $table_name WHERE id = %d", $quote_id ) );

            $current_user = wp_get_current_user();
            $user_name = $current_user->exists() ? $current_user->display_name : 'Admin';
            $timestamp = date( 'M j, Y H:i', current_time( 'timestamp' ) );

            $note_entry = "[{$timestamp} - {$user_name}]\n{$new_note}\n\n";
            $updated_notes = $existing ? $existing . $note_entry : $note_entry;

            $wpdb->update(
                $table_name,
                array( 'internal_notes' => $updated_notes, 'updated_at' => current_time( 'mysql' ) ),
                array( 'id' => $quote_id ),
                array( '%s', '%s' ),
                array( '%d' )
            );

            if ( class_exists( 'BQF_Logger' ) ) {
                BQF_Logger::log_timeline( $quote_id, "Note Added" );
            }

            wp_redirect( add_query_arg( array( 'page' => 'quoteflow-quotes', 'action' => 'view', 'id' => $quote_id, 'note_added' => 'true' ), admin_url( 'admin.php' ) ) );
            exit;
        }
    }

    public function handle_delete_quote() {
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['quote_id'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'bqf_delete_nonce' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'bqf_quotes';
            $quote_id = intval( $_GET['quote_id'] );

            $wpdb->delete(
                $table_name,
                array( 'id' => $quote_id ),
                array( '%d' )
            );

            wp_redirect( add_query_arg( array( 'page' => 'quoteflow-quotes', 'deleted' => 'true' ), admin_url( 'admin.php' ) ) );
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
            <title><?php esc_html_e( 'Quote Request ', 'briones-quoteflow' ); ?><?php echo esc_html( $quote->reference_id ); ?></title>
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
                <button onclick="window.print();" style="padding: 10px 20px; background: #dca54a; color: #fff; border: none; cursor: pointer; border-radius: 4px;"><?php esc_html_e( 'Print / Save as PDF', 'briones-quoteflow' ); ?></button>
            </div>

            <div class="header">
                <h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
                <p><?php esc_html_e( 'Quote Request ', 'briones-quoteflow' ); ?><strong><?php echo esc_html( $quote->reference_id ); ?></strong></p>
                <p><?php esc_html_e( 'Date:', 'briones-quoteflow' ); ?> <?php echo esc_html( date( 'F j, Y', strtotime( $quote->created_at ) ) ); ?></p>
            </div>

            <div class="section">
                <h2><?php esc_html_e( 'Product Details', 'briones-quoteflow' ); ?></h2>
                <table>
                    <tr>
                        <th><?php esc_html_e( 'Product', 'briones-quoteflow' ); ?></th>
                        <td><?php echo esc_html( $quote->product_name ); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Price', 'briones-quoteflow' ); ?></th>
                        <td><?php echo esc_html( $quote->product_price ); ?></td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2><?php esc_html_e( 'Customer Details', 'briones-quoteflow' ); ?></h2>
                <table>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'briones-quoteflow' ); ?></th>
                        <td><?php echo esc_html( $quote->name ); ?></td>
                    </tr>
                    <?php if ( ! empty( $quote->company ) ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'Company', 'briones-quoteflow' ); ?></th>
                        <td><?php echo esc_html( $quote->company ); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php esc_html_e( 'Email', 'briones-quoteflow' ); ?></th>
                        <td><?php echo esc_html( $quote->email ); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Phone', 'briones-quoteflow' ); ?></th>
                        <td><?php echo esc_html( $quote->phone ); ?></td>
                    </tr>
                </table>
            </div>

            <?php if ( ! empty( $quote->message ) ) : ?>
            <div class="section">
                <h2><?php esc_html_e( 'Additional Message', 'briones-quoteflow' ); ?></h2>
                <p style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;"><?php echo nl2br( esc_html( $quote->message ) ); ?></p>
            </div>
            <?php endif; ?>

            <div style="margin-top: 50px; text-align: center; color: #999; font-size: 12px;">
                <p><?php esc_html_e( 'Thank you for your interest.', 'briones-quoteflow' ); ?></p>
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
