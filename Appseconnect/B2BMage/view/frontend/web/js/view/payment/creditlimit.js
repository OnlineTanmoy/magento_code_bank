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
                type: 'creditlimit',
                component: 'Appseconnect_B2BMage/js/view/payment/creditlimit-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);