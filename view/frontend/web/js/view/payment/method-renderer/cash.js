define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'DigitalFemsa_Payments/payment/base-form',
                transactionResult: ''
            },

            getFormTemplate: function(){
                return 'DigitalFemsa_Payments/payment/cash/form'
            },

            initialize: function() {
                var self = this;
                this._super();
            },

            isVisiblePaymentButton: function () {
                return true;
            },

            getCode: function () {
                return 'digitalfemsa_cash';
            },

            isActive: function () {
                return true;
            },

            getGlobalConfig: function() {
                return window.checkoutConfig.payment.femsa_global
            },

            getFemsaLogo: function() {
                return this.getGlobalConfig().femsa_logo;
            },

            /** Returns send check to info */
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            beforePlaceOrder: function () {
            	this.placeOrder();
            }
        });
    }
);
