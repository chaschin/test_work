jQuery.fn.exists = function () {
    return jQuery(this).length > 0;
}

$ = jQuery.noConflict();
jQuery(document).ready(function($) {
    
    $(document).on('click', '.product_buy_btn', function() {       
        $('#orderForm').show();
        $('.order-action').show();
        $('.close-action').hide();
        $('.modal').modal('show');
    });
    
    $(document).on('click', '.submit-order', function() {
        $('.invalid-feedback').remove();
        $('.valid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.is-valid').removeClass('is-valid');
        if (validateFields($('#orderForm')) === false) {
            return false;
        }
        var data = $('#orderForm').serialize();
        $.post(admin_ajax_url, data, function(response) {
            var msg = jQuery.parseJSON(response);
            if (msg['status']) {
                $('#ajax-results').empty();
                $('#ajax-results').append('<div class="alert alert-success" role="alert">' + msg['status_message'] + '</div>');
                $('#orderForm').hide();
                $('.order-action').hide();
                $('.close-action').show();
            } else {
                $.each(msg['errors'], function(i, v) {
                    $('#' + i).removeClass('is-valid');
                    $('#' + i).addClass('is-invalid');
                    $('#' + i).parent().children('.invalid-feedback').remove();
                    $('#' + i).parent().children('.valid-feedback').remove();
                    $('#' + i).parent().append('<div class="invalid-feedback">' + v + '</div>');
                    $('#ajax-results').empty();
                    $('#ajax-results').append('<div class="alert alert-danger" role="alert">' + msg['status_message'] + '</div>');
                });
            }
        });
    });
    
});

function validateFields(formEl) {
    var status = true;
    var regularExpr = /^([a-zA-Z\. ]{2,16})$/;
    $(formEl).find('.need2validate').each(function(i, el) {
        if ($(el).hasClass('validate_email')) {
            regularExpr = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        } else {
            regularExpr = /^([a-zA-Z\. ]{2,16})$/;
        }
        var v = $(el).val();
        if (v == '') {
            status = false;
            $(el).addClass('is-invalid');
            $(el).parent().append('<div class="invalid-feedback">' + messages.empty_field + '</div>');
        } else if ((regularExpr.test(v)) !== true) {
            status = false;
            $(el).addClass('is-invalid');
            $(el).parent().append('<div class="invalid-feedback">' + messages.invalid_field + '</div>');
        } else {
            $(el).addClass('is-valid');
            $(el).parent().append('<div class="valid-feedback">' + messages.validated + '</div>');
        }
    });
    return status;
}