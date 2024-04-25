define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalfemsa_ef',
                component: 'DigitalFemsa_Payments/js/view/payment/method-renderer/embedform'
            }
        );
        return Component.extend({});
    }
);
