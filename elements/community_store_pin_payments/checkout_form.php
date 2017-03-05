<?php defined('C5_EXECUTE') or die(_("Access Denied."));
extract($vars);
?>
<script src="<?= str_replace('/index.php/', '/', URL::to('packages/community_store_pin_payments/js/jquery.payment.min.js'));?>"></script>
<script src='https://cdn.pin.net.au/pin.v2.js'></script>

<script>
    // 1. Wait for the page to load
    $(function() {

        var form = $('#store-checkout-form-group-payment'),
            submitButton = form.find("[data-payment-method-id=\"<?= $pmID; ?>\"] .store-btn-complete-order"),
            pin_errorContainer = form.find('.pin-payment-errors');

        $('#pin-cc-number').payment('formatCardNumber');
        $('#pin-cc-exp').payment('formatCardExpiry');
        $('#pin-cc-cvc').payment('formatCardCVC');

        $('#pin-cc-number').bind("keyup change", function(e) {
            var validcard = $.payment.validateCardNumber($(this).val());

            if (validcard) {
                $(this).closest('.form-group').removeClass('has-error');
            }
        });

        $('#pin-cc-exp').bind("keyup change", function(e) {
            var validcard = $.payment.validateCardNumber($(this).val());

            var expiry = $(this).payment('cardExpiryVal');
            var validexpiry = $.payment.validateCardExpiry(expiry.month, expiry.year);

            if (validexpiry) {
                $(this).closest('.form-group').removeClass('has-error');
            }
        });

        $('#pin-cc-cvc').bind("keyup change", function(e) {
            var validcv = $.payment.validateCardCVC($(this).val());

            if (validcv) {
                $('#pin-cc-cvc').closest('.form-group').removeClass('has-error');
            }
        });

        // 2. Create an API object
        var pinApi = new Pin.Api('<?= $publicAPIKey; ?>', '<?= $mode; ?>');

        // 3. Add a submit handler
        form.submit(function(e) {
            var currentpmid = $('input[name="payment-method"]:checked:first').data('payment-method-id');

            if (currentpmid == <?= $pmID; ?>) {
                e.preventDefault();

                var allvalid = true;

                var validcard = $.payment.validateCardNumber($('#pin-cc-number').val());

                if (!validcard) {
                    $('#pin-cc-number').closest('.form-group').addClass('has-error');
                    allvalid = false;
                } else {
                    $('#pin-cc-number').closest('.form-group').removeClass('has-error');
                }

                var expiry = $('#pin-cc-exp').payment('cardExpiryVal');
                var validexpiry = $.payment.validateCardExpiry(expiry.month, expiry.year);

                if (!validexpiry) {
                    $('#pin-cc-exp').closest('.form-group').addClass('has-error');
                    allvalid = false;
                } else {
                    $('#pin-cc-exp').closest('.form-group').removeClass('has-error');
                }

                var validcv = $.payment.validateCardCVC($('#pin-cc-cvc').val());

                if (!validcv) {
                    $('#pin-cc-cvc').closest('.form-group').addClass('has-error');
                    allvalid = false;
                } else {
                    $('#pin-cc-cvc').closest('.form-group').removeClass('has-error');
                }

                if (!allvalid) {
                    if (!validcard) {
                        $('#pin-cc-number').focus()
                    } else {
                        if (!validexpiry) {
                            $('#pin-cc-exp').focus()
                        } else {
                            if (!validcv) {
                                $('#pin-cc-cvc').focus()
                            }
                        }
                    }

                    return false;
                }

                // Clear previous errors
                pin_errorContainer.empty();
                pin_errorContainer.hide();

                // Disable the submit button to prevent multiple clicks
                submitButton.prop('disabled', true);
                submitButton.val('<?= t('Processing...'); ?>');

                // Fetch details required for the createToken call to Pin Payments
                var card = {
                    number: $('#pin-cc-number').val(),
                    name:   $('#store-checkout-billing-first-name').val() + ' ' + $('#store-checkout-billing-last-name').val(),
                    expiry_month: expiry.month,
                    expiry_year: expiry.year,
                    cvc: $('#pin-cc-cvc').val(),
                    address_line1:    $('#store-checkout-billing-address-1').val(),
                    address_line2:    $('#store-checkout-billing-address-2').val(),
                    address_city:     $('#store-checkout-billing-city').val(),
                    address_state:    $('#store-checkout-billing-state').val(),
                    address_postcode: $('#store-checkout-billing-zip').val(),
                    address_country:  $('#store-checkout-billing-country').val()
                };

                // Request a token for the card from Pin Payments
                pinApi.createCardToken(card).then(pin_handleSuccess, pin_handleError).done();
            } else {
                // allow form to submit normally
            }
        });

        function pin_handleSuccess(card) {
            // Add the card token to the form
            //
            // Once you have the card token on your server you can use your
            // private key and Charges API to charge the credit card.
            $('<input>')
                .attr({type: 'hidden', name: 'pinToken'})
                .val(card.token)
                .appendTo(form);

            // Resubmit the form to the server
            //
            // Only the card_token will be submitted to your server. The
            // browser ignores the original form inputs because they don't
            // have their 'name' attribute set.
            form.get(0).submit();
        }

        function pin_handleError(response) {
            if (response.messages) {
                $.each(response.messages, function(index, paramError) {
                    $('<p class="alert alert-danger">').text(paramError.message).appendTo(pin_errorContainer);
                });
            }

            pin_errorContainer.show();

            // Re-enable the submit button
            submitButton.removeAttr('disabled');
            submitButton.val('<?= t('Complete Order'); ?>');
        };
    });


</script>


<div class="panel panel-default credit-card-box">
    <div class="panel-body">
        <div style="display:none;" class="store-payment-errors pin-payment-errors">
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
                    <label for="cardNumber"><?= t('Card Number');?></label>
                    <div class="input-group">
                        <input
                            type="tel"
                            class="form-control"
                            id="pin-cc-number"
                            placeholder="<?= t('Card Number');?>"
                            autocomplete="cc-number"
                            />
                        <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-7 col-md-7">
                <div class="form-group">
                    <label for="cardExpiry"><?= t('Expiration Date');?></label>
                    <input
                        type="tel"
                        class="form-control"
                        id="pin-cc-exp"
                        placeholder="MM / YY"
                        autocomplete="cc-exp"
                        />
                </div>
            </div>
            <div class="col-xs-5 col-md-5 pull-right">
                <div class="form-group">
                    <label for="cardCVC"><?= t('CV Code');?></label>
                    <input
                        type="tel"
                        class="form-control"
                        id="pin-cc-cvc"
                        placeholder="<?= t('CVC');?>"
                        autocomplete="off"
                        />
                </div>
            </div>
        </div>
    </div>
</div>