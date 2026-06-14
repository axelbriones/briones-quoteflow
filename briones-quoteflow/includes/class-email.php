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

        $subject = sprintf( 'New Quote Request - %s', $product_name );

        $message = "Product: {$product_name}\n";
        $message .= "Price: {$product_price}\n\n";
        $message .= "Customer:\n{$full_name}\n\n";
        $message .= "Email:\n{$email}\n\n";
        $message .= "Phone:\n{$phone}\n\n";
        $message .= "Message:\n{$message_text}\n";

        $headers = array(
            'Reply-To: ' . $full_name . ' <' . $email . '>'
        );

        return wp_mail( $to, $subject, $message, $headers );
    }
}
