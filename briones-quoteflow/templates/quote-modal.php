<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="bqf-quote-modal" class="bqf-modal-overlay">
    <div class="bqf-modal-content">
        <span class="bqf-modal-close">&times;</span>
        <h3><?php esc_html_e( 'Request a Quote', 'briones-quoteflow' ); ?></h3>

        <form id="bqf-quote-form" method="post">
            <input type="hidden" id="bqf_product_id" name="product_id" value="">

            <div class="bqf-form-group">
                <label for="bqf_product"><?php esc_html_e( 'Product', 'briones-quoteflow' ); ?></label>
                <input type="text" id="bqf_product" name="product_name" readonly>
            </div>

            <?php if ( get_option( 'bqf_enable_price', 1 ) ) : ?>
            <div class="bqf-form-group">
                <label for="bqf_price"><?php esc_html_e( 'Price', 'briones-quoteflow' ); ?></label>
                <input type="text" id="bqf_price" name="product_price" readonly>
            </div>
            <?php else: ?>
                <input type="hidden" id="bqf_price" name="product_price" value="">
            <?php endif; ?>

            <div class="bqf-form-group">
                <label for="bqf_full_name"><?php esc_html_e( 'Full Name', 'briones-quoteflow' ); ?> *</label>
                <input type="text" id="bqf_full_name" name="full_name" required>
            </div>

            <?php if ( get_option( 'bqf_field_company', 1 ) ) : ?>
            <div class="bqf-form-group">
                <label for="bqf_company"><?php esc_html_e( 'Company', 'briones-quoteflow' ); ?></label>
                <input type="text" id="bqf_company" name="company">
            </div>
            <?php endif; ?>

            <div class="bqf-form-group">
                <label for="bqf_email"><?php esc_html_e( 'Email', 'briones-quoteflow' ); ?> *</label>
                <input type="email" id="bqf_email" name="email" required>
            </div>

            <?php if ( get_option( 'bqf_field_phone', 1 ) ) : ?>
            <div class="bqf-form-group">
                <label for="bqf_phone"><?php esc_html_e( 'Phone', 'briones-quoteflow' ); ?> *</label>
                <input type="tel" id="bqf_phone" name="phone" pattern="[0-9\+\-\s\(\)]+" title="<?php esc_attr_e('Valid phone number formats allowed', 'briones-quoteflow'); ?>" required>
            </div>
            <?php endif; ?>

            <?php if ( get_option( 'bqf_field_message', 1 ) ) : ?>
            <div class="bqf-form-group">
                <label for="bqf_message"><?php esc_html_e( 'Message', 'briones-quoteflow' ); ?></label>
                <textarea id="bqf_message" name="message" rows="4"></textarea>
            </div>
            <?php endif; ?>

            <?php
            /**
             * Hook to add custom extra fields.
             *
             * For example, developers can hook into this to add 'Quantity', 'Delivery Location', etc.
             * Output should be wrapped in .bqf-form-group div with proper inputs.
             * Add inputs with class `bqf-custom-field` to auto-capture them.
             */
            do_action( 'bqf_after_quote_fields' );
            ?>

            <div style="display:none;">
                <label for="bqf_honeypot"><?php esc_html_e( 'Leave this field empty', 'briones-quoteflow' ); ?></label>
                <input type="text" id="bqf_honeypot" name="bqf_honeypot" value="">
            </div>

            <button type="submit" class="bqf-submit-button"><?php esc_html_e( 'Send Request', 'briones-quoteflow' ); ?></button>
            <div class="bqf-message"></div>
        </form>
    </div>
</div>
