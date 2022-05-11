define(
    [
        'jquery',
        'uiComponent',
        'jquery',
        'jquery/ui',
        'Appseconnect_B2BMage/js/chosen.jquery.min',
        'Appseconnect_B2BMage/js/select2.min',
        'mage/template',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({

            initialize: function (config) {
                var searchUrl = config.searchUrl;

                //Go To Checkout action, redirect to the checkout page
                $(".action.primary.proceed").click(function () {
                    var message;
                    var qty;
                    var check = true;
                    $(".page.messages").find(".messages").html("");
                    if ($(".input-text.qty").length > 0) {
                        $(".input-text.qty").each(function () {
                            qty = $(this).val().trim();
                            var product_detail = $(this).parent().parent().parent().parent().find(".frontend__control-select.productadd").val();
                            if (!product_detail) {
                                check = false;
                                message = "Please select all SKU";
                            } else if (!qty || qty == 0) {
                                check = false;
                                message = "Quantity can't be zero or empty";
                            }
                        })
                    }
                    if (!check) {
                        $(".page.messages").find(".messages:first").html('<div class="message-error error message"><div data-bind="html: message.text">' + message + '</div></div>');
                    } else {
                        $("#quick_order_form").submit();
                    }
                });

            }

        });
    }
);
