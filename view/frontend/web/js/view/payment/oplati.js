define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'oplati',
                component: 'Oplati_Oplati/js/view/payment/method-renderer/oplati-method'
            }
        );
        return Component.extend({});
    }
);
