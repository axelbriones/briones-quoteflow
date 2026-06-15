jQuery(document).ready(function($) {
    // Open Modal
    $('.bqf-quote-button').on('click', function(e) {
        e.preventDefault();

        var productId = $(this).data('product_id');
        var productName = $(this).data('product_name');
        var productPrice = $(this).data('product_price');
        var isVariable = $(this).data('is_variable');

        // Check for variation selections if it's a variable product on single page
        if ( isVariable == '1' && $('.variations_form').length > 0 ) {
            var variationId = $('input[name="variation_id"]').val();
            if ( !variationId || variationId == '0' || variationId == '' ) {
                // bqf_ajax.i18n_select_options is populated via wp_localize_script if needed, fallback for now:
                var alertMsg = typeof bqf_ajax.i18n_select_options !== 'undefined' ? bqf_ajax.i18n_select_options : 'Please select product options before requesting a quote.';
                alert(alertMsg);
                return;
            }

            // Append attributes to product name
            var attributes = [];
            $('.variations select').each(function() {
                var label = $(this).closest('tr').find('.label label').text();
                var val = $(this).find('option:selected').text();
                if(val && val !== 'Choose an option') {
                    attributes.push(label + ': ' + val);
                }
            });

            if (attributes.length > 0) {
                productName += ' (' + attributes.join(', ') + ')';
            }
            productId = variationId; // Use the specific variation ID
        }

        $('#bqf_product_id').val(productId);
        $('#bqf_product').val(productName);
        $('#bqf_price').val(productPrice);

        $('#bqf-quote-modal').css('display', 'flex');
    });

    // Close Modal
    $('.bqf-modal-close').on('click', function() {
        $('#bqf-quote-modal').css('display', 'none');
        $('.bqf-message').hide();
        $('#bqf-quote-form')[0].reset();
    });

    // Close Modal when clicking outside
    $(window).on('click', function(e) {
        if ($(e.target).is('.bqf-modal-overlay')) {
            $('#bqf-quote-modal').css('display', 'none');
            $('.bqf-message').hide();
            $('#bqf-quote-form')[0].reset();
        }
    });

    // Form Submit
    $('#bqf-quote-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $form.find('.bqf-submit-button');
        var $messageDiv = $form.find('.bqf-message');

        $submitButton.prop('disabled', true).text('Sending...');
        $messageDiv.hide().removeClass('success error');

        var data = {
            action: 'bqf_submit_quote',
            nonce: bqf_ajax.nonce,
            product_id: $('#bqf_product_id').val(),
            product_name: $('#bqf_product').val(),
            product_price: $('#bqf_price').val(),
            full_name: $('#bqf_full_name').val(),
            company: $('#bqf_company').val(),
            email: $('#bqf_email').val(),
            phone: $('#bqf_phone').val(),
            message: $('#bqf_message').val(),
            bqf_honeypot: $('#bqf_honeypot').val()
        };

        // Capture any custom fields added via the hook
        var customFields = {};
        $('.bqf-custom-field').each(function() {
            var name = $(this).attr('name');
            var val = $(this).val();
            if ( name ) {
                customFields[name] = val;
            }
        });

        if ( Object.keys(customFields).length > 0 ) {
            data.custom_fields = JSON.stringify(customFields);
        }

        $.post(bqf_ajax.ajax_url, data, function(response) {
            if (response.success) {
                $messageDiv.addClass('success').text(response.data.message).show();
                $form[0].reset();
                setTimeout(function() {
                    $('#bqf-quote-modal').css('display', 'none');
                    $messageDiv.hide();
                }, 3000);
            } else {
                $messageDiv.addClass('error').text(response.data.message).show();
            }
        }).fail(function() {
            $messageDiv.addClass('error').text('An error occurred. Please try again.').show();
        }).always(function() {
            $submitButton.prop('disabled', false).text('Send Request');
        });
    });
});
