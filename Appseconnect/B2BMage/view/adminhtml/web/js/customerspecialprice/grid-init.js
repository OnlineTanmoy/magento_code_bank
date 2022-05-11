/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            specialPriceProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('in_special_price_products').value = Object.toJSON(specialPriceProducts);

        /**
         * Register Special Price Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerSpecialPriceProduct(grid, element, checked)
        {
            if (checked) {
                if (element.specialPriceElement) {
                    element.specialPriceElement.disabled = false;
                    specialPriceProducts.set(element.value, element.specialPriceElement.value);
                }
            } else {
                if (element.specialPriceElement) {
                    element.specialPriceElement.disabled = true;
                }
                specialPriceProducts.unset(element.value);
            }
            $('in_special_price_products').value = Object.toJSON(specialPriceProducts);
            grid.reloadParams = {
                'products[]': specialPriceProducts.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function specialPriceProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product Special Price
         *
         * @param {String} event
         */
        function specialPriceChange(event)
        {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                specialPriceProducts.set(element.checkboxElement.value, element.value);
                $('in_special_price_products').value = Object.toJSON(specialPriceProducts);
            }
        }

        /**
         * Initialize Special Price product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function specialPriceProductRowInit(grid, row)
        {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                specialPrice = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && specialPrice) {
                if (specialPriceProducts.get(checkbox.value)) {
                    specialPrice.value = specialPriceProducts.get(checkbox.value);
                }
                checkbox.specialPriceElement = specialPrice;
                specialPrice.checkboxElement = checkbox;
                specialPrice.disabled = !checkbox.checked;
                specialPrice.tabIndex = tabIndex++;
                Event.observe(specialPrice, 'keyup', specialPriceChange);
            }
        }

        gridJsObject.rowClickCallback = specialPriceProductRowClick;
        gridJsObject.initRowCallback = specialPriceProductRowInit;
        gridJsObject.checkboxCheckCallback = registerSpecialPriceProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                specialPriceProductRowInit(gridJsObject, row);
            });
        }
    };
});

