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

        $product_id = intval( $data['product_id'] );
        $product_url = get_permalink( $product_id );

        $sku = '';
        $image_url = '';
        if ( $product_id ) {
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

        $message = "Product: {$product_name}\n";
        $message .= "Price: {$product_price}\n";
        if ( ! empty( $sku ) ) {
            $message .= "SKU: {$sku}\n";
        }
        $message .= "Product URL: {$product_url}\n";
        if ( ! empty( $image_url ) ) {
            $message .= "Featured Image: {$image_url}\n";
        }
        $message .= "\nCustomer:\n{$full_name}\n\n";
        $message .= "Email:\n{$email}\n\n";
        $message .= "Phone:\n{$phone}\n\n";
        $message .= "Message:\n{$message_text}\n";

        $headers = array(
            'Reply-To: ' . $full_name . ' <' . $email . '>'
        );

        return wp_mail( $to, $subject, $message, $headers );
    }

    public static function send_customer_confirmation( $data ) {
        $to = sanitize_email( $data['email'] );
        $product_name = sanitize_text_field( $data['product_name'] );
        $full_name = sanitize_text_field( $data['full_name'] );
        $product_price = sanitize_text_field( $data['product_price'] );

        $subject_template = get_option( 'bqf_email_customer_subject', 'We have received your quote request' );
        $subject = str_replace(
            array( '{product_name}', '{customer_name}', '{product_price}' ),
            array( $product_name, $full_name, $product_price ),
            $subject_template
        );

        $default_body = "Hello {customer_name},\n\nThank you for contacting us.\n\nProduct: {product_name}\n\nOur team will review your request and contact you shortly.";
        $body_template = get_option( 'bqf_email_customer_body', $default_body );
        $message = str_replace(
            array( '{product_name}', '{customer_name}', '{product_price}' ),
            array( $product_name, $full_name, $product_price ),
            $body_template
        );

        $from_email = get_option( 'admin_email' );
        $from_name = get_bloginfo( 'name' );

        $headers = array(
            'From: ' . $from_name . ' <' . $from_email . '>'
        );

        return wp_mail( $to, $subject, $message, $headers );
    }
}
