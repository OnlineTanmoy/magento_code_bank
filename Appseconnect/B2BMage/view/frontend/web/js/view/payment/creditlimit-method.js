define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
 
        return Component.extend({
            defaults: {
                template: 'Appseconnect_B2BMage/payment/creditlimit'
            },
 
            /** Returns send check to info */
            /*getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },*/
            /** Returns payment method instructions */
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }
        });
    }
);