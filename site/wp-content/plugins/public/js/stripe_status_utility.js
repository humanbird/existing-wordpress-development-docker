(function ($) {
    $(document).ready(function () {
        $.ajax({
            url: rm_ajax.url,
            type: 'POST',
            data: {action: 'rm_stripe_localize_data', 'rm_sec_nonce': rm_admin_vars.nonce},
            async: true,
            success: function (result) {
                const stripe_keys = result;
                const stripe = Stripe(stripe_keys.public, {
                    locale: stripe_keys.locale
                });
                const sub_id = $("form#rm-stripe-payment-form").data("submission-id");
                const log_id = $("form#rm-stripe-payment-form").data("log-id");
                const total_price = $("form#rm-stripe-payment-form").data("total-price");
                const description = $("form#rm-stripe-payment-form").data("description");
                const container = $('div.rm_stripe_fields');
                let elements;
        
                checkStatus();
                        
                // Fetches the payment intent status after payment submission
                async function checkStatus() {
                    const clientSecret = new URLSearchParams(window.location.search).get(
                        "payment_intent_client_secret"
                    );
                    
                    if (!clientSecret) {
                        return;
                    }
        
                    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
        
                    switch (paymentIntent.status) {
                        case "succeeded":
                            showMessage("Payment succeeded!");
                            var data = {action: 'rm_stripe_after_intent', 'rm_sec_nonce': rm_admin_vars.nonce, intent_status: paymentIntent.status, intent: paymentIntent, total_price: total_price, sub_id: sub_id, current_url: get_current_url(), log_id: log_id, description: description};
                            $.ajax({
                                url: rm_ajax.url,
                                type: 'POST',
                                data: data,
                                async: true,
                                success: function (success_response) {
                                    container.html(success_response.data.msg);
                                    if (success_response.data.redirect) {
                                        location.href = success_response.data.redirect;
                                    }
                                    if (success_response.data.hasOwnProperty('reload_params')) {
                                        var url = [location.protocol, '//', location.host, location.pathname].join('');
                                        if(url.indexOf('admin-ajax.php')>=0){
                                            return;
                                        }
                                        url += success_response.data.reload_params;
                                        location.href = url;
                                    }
                                }
                            });
                            break;
                        case "processing":
                            showMessage("Your payment is processing.");
                            break;
                        case "requires_payment_method":
                            showMessage("Your payment was not successful, please try again.");
                            break;
                        default:
                            showMessage("Something went wrong.");
                            break;
                    }
                }
                
                function get_current_url() {
                    return location.protocol + '//' + location.host + location.pathname;
                }
        
                // ------- UI helpers -------
                
                function showMessage(messageText) {
                    const messageContainer = document.querySelector("#rm-stripe-payment-message");
        
                    messageContainer.classList.remove("rm-stripe-hidden");
                    messageContainer.textContent = messageText;
        
                    setTimeout(function () {
                        messageContainer.classList.add("rm-stripe-hidden");
                        messageText.textContent = "";
                    }, 4000);
                }
            },
            error: function (result) {
                alert('Unable to retrieve Stripe key. Please try again.');
                return;
            }
        });
    });
})(jQuery);