jQuery(document).ready(function($) {
    // Open Modal
    $('.bqf-quote-button').on('click', function(e) {
        e.preventDefault();

        var productId = $(this).data('product_id');
        var productName = $(this).data('product_name');
        var productPrice = $(this).data('product_price');

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
            message: $('#bqf_message').val()
        };

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
