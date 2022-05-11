define(
    [
        'jquery',
        'uiComponent',
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

                var removeUrl = config.removeUrl;
                var formKey = config.formKey;
                var addUrl = config.addUrl;
                var updateUrl = config.updateUrl;

                $('#my-orders-table').on('click', '.inc,.dec', function () {
                    var className = $(this).attr('class').trim();
                    var increment_value = $(this).attr('inc');
                    increment_value = parseInt(increment_value);
                    if (increment_value == 0) {
                        increment_value = 1;
                    }
                    var minSaleQty = $(this).attr('min');
                    minSaleQty = parseInt(minSaleQty);
                    var qty = parseInt($(this).parent().find('.input-text.qty').val());
                    var new_qty = qty + increment_value;
                    if (className == 'dec') {
                        new_qty = qty - increment_value;
                    }
                    if (new_qty >= minSaleQty) {
                        $(this).parent().find('.input-text.qty').val(new_qty)
                    }
                    updateQuantity($(this).parent().find('.input-text.qty'));
                    return false;
                });
                //to increment or decrement

                //Product Selection using search box
                $(".select.frontend__control-select.productadd").select2({
                    placeholder: "Search with SKU",
                    ajax: {
                        url: searchUrl,
                        dataType: 'json',
                        delay: 700,
                        data: function (params) {
                            return {
                                productSku: params.term // search term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 3
                });


                //Add More Product action, creates a new row
                $(".action.primary.add").click(function () {
                    var count = document.getElementById("count").value;
                    document.getElementById("count").value = parseInt(count) + 1;
                    var table_data = '<tr>'
                        + '<td class="col productsku" width="550">'
                        + '<select class="select frontend__control-select productadd" id="product_sku' + count + '" name="product_sku[]">'
                        + '</select></td>'
                        + '<td class="col qty" data-th="Qty">'
                        + '<div class="field qty">'
                        + '<div style="white-space:nowrap;" class="control qty">'
                        + '<button class="dec" style="width: 35px;height: 35px;">-</button>'
                        + '<input style="width:80px; margin:0 3px 0 4px;" name="qty[]" value="" type="number" id="qty' + count + '" size="4" title="Qty" class="input-text qty" maxlength="12" data-validate="{required:true,validate-greater-than-zero:true}" data-role="cart-item-qty">'
                        + '<button class="inc" style="width: 35px;height: 35px;">+</button>'
                        + '</div>'
                        + '</div></td>'
                        + '<td class="col action"><a href="javascript:;" class="delete row">Remove</a></td> '
                        + '</tr>';
                    jQuery("#cart_item").append(table_data);

                    jQuery(".input-text.qty").keyup(function () {
                        updateQuantity(jQuery(this));
                    });


                    //Check if a product already exists or not in the cart
                    function checkProduct() {
                        var update = "";
                        var obj = "";
                        $(".select.frontend__control-select.productadd").each(function () {
                            var sku = $(this).val();
                            if (sku) {
                                //var position = sku.lastIndexOf(" min_qty ");
                                var obj = jQuery.parseJSON(sku);

                                sku = obj.sku;
                                update += '{"sku":"' + sku + '"},';
                            }
                        });
                        if (update) {
                            var data = '{"data":[' + update + ']}';
                            obj = eval("(" + data + ")");
                        }
                        return obj;
                    }

                    //Product Selection using search box
                    $(".select.frontend__control-select.productadd").select2({
                        placeholder: "Search with SKU",
                        ajax: {
                            url: searchUrl,
                            dataType: 'json',
                            delay: 800,
                            data: function (params) {
                                var object = checkProduct();
                                return {
                                    object: object,
                                    productSku: params.term // search term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 3
                    });
                });

                $(".input-text.qty").keyup(function () {
                    updateQuantity($(this));
                });

                $(".input-text.qty").keypress(function () {
                    isNumberKey($(this));
                });

                $('#my-orders-table').on('click', '.delete.row', function () {
                    deleteRow($(this));
                });

                $('#my-orders-table').on('change', '.select.frontend__control-select.productadd,.select.frontend__control-select.productadd', function () {
                    addProductToCart($(this));
                });


                var deleteRow = function (currentObject) {
                    $('<div />').html('Are you sure you would like to remove this item?')
                        .modal({
                            autoOpen: true,
                            closed: function () {
                            },
                            buttons: [{
                                text: 'Cancel',
                                attr: {
                                    'data-action': 'confirm'
                                },
                                'class': 'action secondary action-dismiss',
                                click: function () {
                                    this.closeModal();
                                }
                            },
                                {
                                    text: 'OK',
                                    attr: {
                                        'data-action': 'confirm'
                                    },
                                    'class': 'action primary action-accept',
                                    click: function () {
                                        var sku = $(currentObject).parent().parent().find('.select.frontend__control-select.productadd').val();
                                        $(currentObject).parent().parent().remove();
                                        //var rowIndex = currentObject.parentNode.parentNode.rowIndex;
                                        //document.getElementById("my-orders-table").deleteRow(rowIndex);
                                        this.closeModal();
                                        var quote_item_id = 0;


                                        var obj = $.parseJSON(sku);

                                        //var positionOfMinQty = sku.lastIndexOf(" min_qty ");
                                        var selectedSku = obj.sku;
                                        if (selectedSku != '') {
                                            $(".item-qty.cart-item-qty").each(function () {
                                                var item_sku = $(this).attr('data-cart-item-id');
                                                if (selectedSku == item_sku) {
                                                    quote_item_id = $(this).attr('data-cart-item');
                                                }
                                            });
                                            var urlData = removeUrl;
                                            $.ajax({
                                                url: urlData,
                                                data: {form_key: formKey, item_id: quote_item_id},
                                                type: 'POST',
                                                showLoader: true,
                                                //                                  showLoader: true
                                            }).done(function (data) {
                                            });
                                        }
                                    }
                                }]
                        });
                };

                var isNumberKey = function (evt) {
                    var charCode = (evt.which) ? evt.which : event.keyCode;
                    if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
                        return false;
                    }
                    return true;
                };

                var addProductToCart = function (object) {
                    var sku = $(object).val();
                    var obj = $.parseJSON(sku);

                    /* var positionOfMinQty = sku.lastIndexOf(" min_qty ");
                    var positionOfProductId = sku.lastIndexOf(" product_id "); */
                    var productId = obj.product_id;
                    var selectedSku = obj.sku;
                    var quantity = obj.min_qty;
                    var qtyIncrements = obj.qty_increments;
                    $(object).parent().parent().find('.dec').attr('inc', qtyIncrements);
                    $(object).parent().parent().find('.dec').attr('min', quantity);
                    $(object).parent().parent().find('.inc').attr('inc', qtyIncrements);
                    $(object).parent().parent().find('.inc').attr('min', quantity);
                    $(object).parent().parent().find('.input-text.qty').val(quantity);
                    $(object).parent().parent().find('.input-text.qty').attr('original_qty', '' + quantity + '');
                    $(object).find('option').text(selectedSku);
                    $(object).parent().find('.select2-selection__rendered').html(selectedSku);
                    var urlData = addUrl;
                    $.ajax({
                        url: urlData,
                        data: {form_key: window.FORM_KEY, product: productId, qty: quantity},
                        type: 'POST',
                        showLoader: true,
                    }).done(function (data) {
                        if (typeof data.backUrl !== typeof undefined) {
                            errorPopUp("We don't have as many " + '"' + selectedSku + '"' + " as you requested.");
                        }
                    });
                };

                var updateQuantity = function (currentInstant) {
                    var currentInstant = currentInstant;
                    var quote_item_id = 0;
                    var sku = $(currentInstant).parent().parent().parent().parent().find('.select.frontend__control-select.productadd').val();

                    var obj = $.parseJSON(sku);


                    var selectedSku = obj.sku;
                    var originalQty = $(currentInstant).attr('original_qty');
                    var newQty = Number($(currentInstant).val().trim());
                    if (newQty == 0) {
                        errorPopUp("Invalid quantity provided for " + selectedSku + ".");
                    } else if (newQty != 0 && newQty != '') {
                        $(".item-qty.cart-item-qty").each(function () {
                            var item_sku = $(this).attr('data-cart-item-id');
                            if (selectedSku == item_sku) {
                                quote_item_id = $(this).attr('data-cart-item');
                            }
                        });
                        var urlData = updateUrl;
                        $.ajax({
                            url: urlData,
                            data: {form_key: window.FORM_KEY, item_id: quote_item_id, item_qty: newQty},
                            type: 'POST',
                            showLoader: true,
                            //                      showLoader: true
                        }).done(function (data) {
                            if (data.success == false) {
                                errorPopUp("We don't have as many " + '"' + selectedSku + '"' + " as you requested.");
                            }
                            $(currentInstant).val(newQty);
                            $(currentInstant).attr('original_qty', '' + newQty + '');
                        });
                    }
                };

                var errorPopUp = function (message) {
                    $('<div />').html(message)
                        .modal({
                            autoOpen: true,
                            closed: function () {
                            },
                            buttons: [
                                {
                                    text: 'OK',
                                    attr: {
                                        'data-action': 'confirm'
                                    },
                                    'class': 'action primary action-accept',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }]
                        });

                }
            }


        });
    }
);
