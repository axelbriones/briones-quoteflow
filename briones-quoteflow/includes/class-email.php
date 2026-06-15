<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Email {

    public static function send_quote_email( $data ) {
        $to = get_option( 'bqf_notification_email', get_option( 'admin_email' ) );
        $product_name = sanitize_text_field( $data['product_name'] );
        $product_price = sanitize_text_field( $data['product_price'] );
        $full_name = sanitize_text_field( $data['full_name'] );
        $email = sanitize_email( $data['email'] );
        $phone = sanitize_text_field( $data['phone'] );
        $message_text = sanitize_textarea_field( $data['message'] );

        $company = isset( $data['company'] ) ? sanitize_text_field( $data['company'] ) : '';
        $reference_id = isset( $data['reference_id'] ) ? sanitize_text_field( $data['reference_id'] ) : '';

        $product_id = intval( $data['product_id'] );
        $product_url = get_permalink( $product_id );

        $sku = '';
        $image_url = '';
        if ( $product_id && function_exists( 'wc_get_product' ) ) {
            $product = wc_get_product( $product_id );
            if ( $product ) {
                $sku = $product->get_sku();
                $image_id = $product->get_image_id();
                if ( $image_id ) {
                    $image_url = wp_get_attachment_url( $image_id );
                }
            }
        }

        $subject_template = get_option( 'bqf_email_admin_subject', 'New Quote Request - {product_name}' );
        $subject = str_replace(
            array( '{product_name}', '{customer_name}', '{product_price}' ),
            array( $product_name, $full_name, $product_price ),
            $subject_template
        );

        $message  = '<div style="background:#f5f5f5; padding:30px; font-family:Helvetica,Arial,sans-serif;">';
        $message .= '<div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05);">';
        $message .= '<div style="background:#dca54a; padding:20px; color:#ffffff; text-align:center;">';
        $message .= '<h2 style="margin:0;">' . esc_html__( 'New Quote Request', 'briones-quoteflow' ) . '</h2>';
        $message .= '</div>';
        $message .= '<div style="padding:30px; color:#333333;">';

        if ( ! empty( $reference_id ) ) {
            $message .= '<p style="text-align:center; font-size:16px; margin-bottom:20px; color:#666;">' . esc_html__( 'Reference ID:', 'briones-quoteflow' ) . ' <strong>' . esc_html( $reference_id ) . '</strong></p>';
        }

        if ( ! empty( $image_url ) ) {
            $message .= '<div style="text-align:center; margin-bottom:20px;"><img src="' . esc_url( $image_url ) . '" style="max-width:200px; border-radius:4px;" /></div>';
        }

        $message .= '<h3 style="border-bottom:1px solid #eeeeee; padding-bottom:10px; margin-top:0;">' . esc_html__( 'Product Details', 'briones-quoteflow' ) . '</h3>';
        $message .= '<p><strong>' . esc_html__( 'Product:', 'briones-quoteflow' ) . '</strong> <a href="' . esc_url( $product_url ) . '" style="color:#dca54a;">' . esc_html( $product_name ) . '</a></p>';
        $message .= '<p><strong>' . esc_html__( 'Price:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $product_price ) . '</p>';

        if ( ! empty( $sku ) ) {
            $message .= '<p><strong>' . esc_html__( 'SKU:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $sku ) . '</p>';
        }

        $message .= '<h3 style="border-bottom:1px solid #eeeeee; padding-bottom:10px; margin-top:30px;">' . esc_html__( 'Customer Details', 'briones-quoteflow' ) . '</h3>';
        $message .= '<p><strong>' . esc_html__( 'Name:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $full_name ) . '</p>';

        if ( ! empty( $company ) ) {
            $message .= '<p><strong>' . esc_html__( 'Company:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $company ) . '</p>';
        }

        $message .= '<p><strong>' . esc_html__( 'Email:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $email ) . '</p>';

        if ( ! empty( $phone ) ) {
            $message .= '<p><strong>' . esc_html__( 'Phone:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $phone ) . '</p>';
        }

        if ( ! empty( $message_text ) ) {
            $message .= '<h3 style="border-bottom:1px solid #eeeeee; padding-bottom:10px; margin-top:30px;">' . esc_html__( 'Message', 'briones-quoteflow' ) . '</h3>';
            $message .= '<div style="background:#f9f9f9; padding:15px; border-left:4px solid #dca54a;">' . nl2br( esc_html( $message_text ) ) . '</div>';
        }

        if ( isset( $data['quote_id'] ) ) {
            $view_url = admin_url( 'admin.php?page=quoteflow-quotes&action=view&id=' . intval( $data['quote_id'] ) );
            $message .= '<div style="text-align:center; margin-top:30px;">';
            $message .= '<a href="' . esc_url( $view_url ) . '" style="display:inline-block; background:#dca54a; color:#ffffff; text-decoration:none; padding:12px 25px; border-radius:4px; font-weight:bold;">' . esc_html__( 'View Quote Request', 'briones-quoteflow' ) . '</a>';
            $message .= '</div>';
        }

        $message .= '</div>';
        $message .= '<div style="background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#999999;">';
        $message .= esc_html__( 'Powered by Briones QuoteFlow', 'briones-quoteflow' );
        $message .= '</div>';
        $message .= '</div>';
        $message .= '</div>';

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $full_name . ' <' . $email . '>'
        );

        return wp_mail( $to, $subject, $message, $headers );
    }

    public static function send_customer_confirmation( $data ) {
        $to = sanitize_email( $data['email'] );
        $product_name = sanitize_text_field( $data['product_name'] );
        $full_name = sanitize_text_field( $data['full_name'] );
        $product_price = sanitize_text_field( $data['product_price'] );
        $reference_id = isset( $data['reference_id'] ) ? sanitize_text_field( $data['reference_id'] ) : '';

        $product_id = intval( $data['product_id'] );
        $image_url = '';
        if ( $product_id && function_exists( 'wc_get_product' ) ) {
            $product = wc_get_product( $product_id );
            if ( $product ) {
                $image_id = $product->get_image_id();
                if ( $image_id ) {
                    $image_url = wp_get_attachment_url( $image_id );
                }
            }
        }

        $subject_template = get_option( 'bqf_email_customer_subject', 'We have received your quote request' );
        $subject = str_replace(
            array( '{product_name}', '{customer_name}', '{product_price}' ),
            array( $product_name, $full_name, $product_price ),
            $subject_template
        );

        $default_body = "Hello {customer_name},\n\nThank you for contacting us.\n\nProduct: {product_name}\n\nOur team will review your request and contact you shortly.";
        $body_template = get_option( 'bqf_email_customer_body', $default_body );
        $body_content = str_replace(
            array( '{product_name}', '{customer_name}', '{product_price}' ),
            array( $product_name, $full_name, $product_price ),
            $body_template
        );

        $message  = '<div style="background:#f5f5f5; padding:30px; font-family:Helvetica,Arial,sans-serif;">';
        $message .= '<div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05);">';
        $message .= '<div style="background:#dca54a; padding:20px; color:#ffffff; text-align:center;">';
        $message .= '<h2 style="margin:0;">' . esc_html__( 'Quote Request Received', 'briones-quoteflow' ) . '</h2>';
        $message .= '</div>';
        $message .= '<div style="padding:30px; color:#333333; line-height:1.6;">';
        $message .= nl2br( esc_html( $body_content ) );

        $message .= '<div style="margin-top:30px; padding-top:20px; border-top:1px solid #eeeeee;">';
        if ( ! empty( $reference_id ) ) {
            $message .= '<p><strong>' . esc_html__( 'Reference ID:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $reference_id ) . '</p>';
        }

        $message .= '<p><strong>' . esc_html__( 'Product:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $product_name ) . '</p>';
        $message .= '<p><strong>' . esc_html__( 'Price:', 'briones-quoteflow' ) . '</strong> ' . esc_html( $product_price ) . '</p>';

        if ( ! empty( $image_url ) ) {
            $message .= '<div style="margin-top:20px;"><img src="' . esc_url( $image_url ) . '" style="max-width:200px; border-radius:4px;" /></div>';
        }
        $message .= '</div>';

        $message .= '</div>';
        $message .= '<div style="background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#999999;">';
        $message .= esc_html( get_bloginfo( 'name' ) );
        $message .= '</div>';
        $message .= '</div>';
        $message .= '</div>';

        $from_email = get_option( 'admin_email' );
        $from_name = get_bloginfo( 'name' );

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>'
        );

        return wp_mail( $to, $subject, $message, $headers );
    }
}
