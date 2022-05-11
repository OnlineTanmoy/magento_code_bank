/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Appseconnect_B2BMage/checkout/summary/customer_discount'
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function () {
                return this.isFullMode();
            },
            getValue: function () {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('customer_discount').value;
                }
                return this.getFormattedPrice(price);
            },
            getLabel: function () {
                var label = '';
                if (this.totals()) {
                    label = totals.getSegment('customer_discount').title;
                }
                return label;
            },
            getBaseValue: function () {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_customer_discount;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            }
        });
    }
);
