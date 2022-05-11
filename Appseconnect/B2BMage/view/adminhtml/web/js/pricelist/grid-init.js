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
            pricelistProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('in_pricelist_products').value = Object.toJSON(pricelistProducts);

        /**
         * Register Pricelist Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerPricelistProduct(grid, element, checked)
        {
            var isManual = 0;
            if (checked) {
                if (element.finalPriceElement) {
                    if(element.isManual.checked) {
                        element.finalPriceElement.disabled = false;
                        isManual = 1;
                    } else if(!element.isManual.checked) {
                        element.finalPriceElement.disabled = true;
                        element.finalPriceElement.value = parseFloat(element.finalPriceElement.getAttribute("data_price"));
                        isManual = 0;
                    }
                    pricelistProducts.set(element.value, element.finalPriceElement.value+'__'+isManual);
                }
            } else {
                if (element.finalPriceElement) {
                    if(!element.isManual.checked) {
                        element.finalPriceElement.disabled = true;
                    }
                }
                pricelistProducts.unset(element.value);
            }
            $('in_pricelist_products').value = Object.toJSON(pricelistProducts);
            grid.reloadParams = {
                'products[]': pricelistProducts.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function pricelistProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                if(Event.element(event).type == 'checkbox') {
                    checkbox = Element.getElementsBySelector(trElement, 'input');

                    if (checkbox[0]) {
                        checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                        gridJsObject.setCheckboxChecked(checkbox[0], checked);
                    }
                }
            }
        }

        /**
         * Change product finalPrice
         *
         * @param {String} event
         */
        function finalPriceChange(event)
        {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                pricelistProducts.set(element.checkboxElement.value, element.value+'__1');
                $('in_pricelist_products').value = Object.toJSON(pricelistProducts);
            }
        }

        /**
         * Initialize pricelist product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function pricelistProductRowInit(grid, row)
        {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                checkboxManual = $(row).getElementsByClassName('input-checkbox')[0],
                finalPrice = $(row).getElementsByClassName('input-text')[0],
            discountFactor = document.getElementById('page_discount_factor'),
            calculateDiscountFactor = document.getElementById('page_calculate_discount_factor');

            calculateDiscountFactor.value = discountFactor.value;

            if (checkbox && finalPrice) {
                if (pricelistProducts.get(checkbox.value)) {
                    var price = pricelistProducts.get(checkbox.value).split('__');
                    finalPrice.value = price[0];
                }
                checkbox.finalPriceElement = finalPrice;
                checkbox.isManual = checkboxManual;
                finalPrice.checkboxElement = checkbox;
                checkbox.mainFactor = calculateDiscountFactor;
                finalPrice.disabled = !checkboxManual.checked;
                finalPrice.tabIndex = tabIndex++;
                Event.observe(finalPrice, 'keyup', finalPriceChange);
            }
        }

        gridJsObject.rowClickCallback = pricelistProductRowClick;
        gridJsObject.initRowCallback = pricelistProductRowInit;
        gridJsObject.checkboxCheckCallback = registerPricelistProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                pricelistProductRowInit(gridJsObject, row);
            });
        }
    };
});

