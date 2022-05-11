define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'sidebar',
    'mage/translate',
    'mage/dropdown',
    'mage/url'
], function (Component, customerData, $, ko, _, url) {
    'use strict';
    var miniCart = $('[data-block=\'minicart\']');

    var mixin = {
        isButtonEnable: function () {
            var ismaxQuatity = window.checkout.ismaxQuantity;
            if (ismaxQuatity) {
                return true;
            }
            return false;

        },
        /**
         * Update mini shopping cart content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {
            _.each(updatedCart, function (value, key) {
                if (!this.cart.hasOwnProperty(key)) {
                    this.cart[key] = ko.observable();
                }
                this.cart[key](value);
            }, this);
            $.ajax({
                url: $('#availabletopromise_url').val(),
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                if (data) {
                    $('#top-cart-btn-checkout').show();
                } else {
                    $('#top-cart-btn-checkout').hide();
                }
            });
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});