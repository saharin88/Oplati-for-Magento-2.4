define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/url'
    ],
    function (Component, redirectOnSuccessAction, url) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Oplati_Oplati/payment/oplati'
            },
            afterPlaceOrder: function (order_id) {
                redirectOnSuccessAction.redirectUrl = url.build('oplati/payment/processing');
                this.redirectAfterPlaceOrder = true;
            }
        });
    }
);
