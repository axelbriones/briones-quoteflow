<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BQF_Pro_Custom_Fields {

    public function __construct() {
        // Hook into the Free version's form template
        add_action( 'bqf_after_quote_fields', array( $this, 'render_custom_fields' ) );
    }

    public function render_custom_fields() {
        $fields = get_option( 'bqf_pro_custom_fields', array() );

        if ( ! is_array( $fields ) || empty( $fields ) ) {
            return;
        }

        foreach ( $fields as $index => $field ) {
            $label    = isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';
            $type     = isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : 'text';
            $required = isset( $field['required'] ) && $field['required'] == '1' ? 'required' : '';
            $req_mark = $required ? ' *' : '';

            if ( empty( $label ) ) {
                continue;
            }

            // Create a safe ID attribute for the input
            $safe_id = 'bqf_custom_' . sanitize_title( $label ) . '_' . $index;

            echo '<div class="bqf-form-group">';
            echo '<label for="' . esc_attr( $safe_id ) . '">' . esc_html( $label . $req_mark ) . '</label>';

            // Output standard input taking advantage of the Free version's `bqf-custom-field` class logic
            echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $safe_id ) . '" name="' . esc_attr( $label ) . '" class="bqf-custom-field" ' . esc_attr( $required ) . '>';

            echo '</div>';
        }
    }
}
