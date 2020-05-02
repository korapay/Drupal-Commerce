(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.korapayForm = {
        attach: function (context) {
            const options = JSON.parse(drupalSettings.korapay.transactionData)

            const $paymentForm = $('.payment-redirect-form', context)

            $paymentForm.on('submit', function () {

                Korapay.initialize({
                    key: options.public_key,
                    reference: options.reference + '_' + new Date().getTime(),
                    amount: Number(options.amount),
                    currency: 'NGN',
                    customer:{
                        name: `${options.first_name} ${options.last_name}`,
                        email: options.email
                    },
                    onClose: function () {
                        // Simulate an HTTP redirect to the payment cancelled url
                        window.location.replace(options.cancel_action)
                    },
                    onSuccess: function (data) {
                        // Simulate an HTTP redirect to the payment return url
                        window.location.replace(options.redirect_url + `?resp=success&reference=${data.reference}`);
                    },
                    onFailed: function (data) {
                        // Simulate an HTTP redirect to
                        window.location.replace(options.redirect_url + `?resp=failed&reference=${data.reference}`)
                    }
                })
                return false;
            });

            // Trigger form submission when user visits Payment page.
            $paymentForm.once('getPaid').trigger('submit');
        }
    };

})(jQuery, Drupal, drupalSettings);