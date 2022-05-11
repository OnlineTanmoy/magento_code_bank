define(
    [
    'jquery',
    'uiComponent',
    'Appseconnect_B2BMage/js/customertierprice/chosen.jquery.min',
    'Appseconnect_B2BMage/js/customertierprice/select2.min'
    ],
    function ($, Component) {
    'use strict';
    return Component.extend({
        initialize: function (config) {
            var tierpriceaddurl = config.tierpriceaddurl;
            var productlisturl = config.productlisturl;
            var productlistid = config.productlistid;
            //submit button work
            $('.action-default.scalable.submit').click(function () {
                var iserror = false;
                var skuCheck = [];
                var update = "";
                var text = "";
                $('.error-messages').remove();
                $(".select.admin__control-select.productadd").each(function () {
                    var sku = $(this).val();
                    var tierprice_id = $(this).parent().find('.tierprice_id').val();
                    var quantity = $(this).parent().parent().find('.quantity').val();
                    var tierPrice = $(this).parent().parent().find('.tier_price').val();

                    if (sku != '' && quantity != '' && tierPrice != '' && tierPrice != '' && iserror == false) {
                        var checkQuentity = skuCheck[sku];
                        if (typeof checkQuentity != 'undefined' && checkQuentity == quantity) {
                            iserror = true;
                            var message = '<div class="error-messages"><div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-error">Can\'t have same qouantity ( ' + quantity + ' ) with same sku(' + sku + ').</div></div></div></div>';
                            $('#approver_errormesage').append(message);
                            return false;
                        } else {
    update += '{"product_sku":"' + sku + '","quantity":"' + quantity + '","tier_price":"' + tierPrice + '","id":"' + tierprice_id + '" },'; }

                        skuCheck[sku] = quantity;
                    }
                });
                if ($(".select.admin__control-select.product").length && iserror == '') {
                    $(".select.admin__control-select.product").each(function () {
                        var sku = $(this).val();
                        var quantity = $(this).parent().parent().find('.quantity').val();
                        var tierPrice = $(this).parent().parent().find('.tier_price').val();
                        if (sku != '' && quantity != '' && tierPrice != '' && iserror == false) {
                            var checkQuentity = skuCheck[sku];
                            if (typeof checkQuentity != 'undefined' && checkQuentity == quantity) {
                                iserror = true;
                                var message = '<div class="error-messages"><div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-error">Can\'t have same qouantity ( ' + quantity + ' ) with same sku(' + sku + ').</div></div></div></div>';
                                $('#approver_errormesage').append(message);
                                return false;
                            } else {
    text += '{"product_sku":"' + sku + '","quantity":"' + quantity + '","tier_price":"' + tierPrice + '" },'; }
                            skuCheck[sku] = quantity;
                        }
                    });
                }
                if (iserror == false && (text != '' || update != '')) {
                    var Data = '{"insert_data":[' + text + '],"update_data":[' + update + ']}';
                    var obj = eval("(" + Data + ")");
                    jQuery("#tierprice_load").css("display", "block");
                    jQuery.ajax({
                        url: tierpriceaddurl,
                        data: {
                            form_key: window.FORM_KEY,
                            data: obj,
                            tierPriceId: productlistid,
                            action: 'process'
                        },
                        type: 'POST'
                    }).done(function (data) {
                        location.reload();
                    });
                }

            });
            //submit button work end
            //product selection dropdown work
            $(".select.admin__control-select.productadd").select2({
                placeholder: "Search for an SKU",
                ajax: {
                    url: productlisturl,
                    dataType: 'json',
                    delay: 700,
                    data: function (params) {
                        return {
                            productSku: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3
            });
            //$(document).ready(function() {
                $(".select.admin__control-select.product").select2();
                $('.action-default.scalable.add').click(function () {
                    var count = document.getElementById("count").value;
                    document.getElementById("count").value = parseInt(count) + 1;
                    var table_data = '<tr>' +
                        '<td class="col-productsku">' +
                        '<select class="select admin__control-select product" id="product_sku' + count + '">' +
                        '</select></td>' +
                        '<td class="col-quantity"><input type="text" class="quantity"   id="quantity' + count + '" value=""></td>' +
                        '<td class="col-tier_price"><input type="text" class="tier_price"   id="tier_price' + count + '" value=""></td>' +
                        '<td class="col-deletebutton"><button class="delete-row">Delete</button></td> ' +
                        '</tr>';
                    jQuery("#listing_body").append(table_data);

                    //select 2 work
                    $(".select.admin__control-select.product").select2({
                        placeholder: "Search for an SKU",
                        ajax: {
                            url: productlisturl,
                            dataType: 'json',
                            delay: 700,
                            data: function (params) {
                                return {
                                    productSku: params.term // search term
                                };
                            },
                            processResults: function (data) {
                                // parse the results into the format expected by Select2.
                                // since we are using custom formatting functions we do not need to
                                // alter the remote JSON data
                                return {
                                    results: data
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 3
                    });
                });
            //});
            $("#tiers_table").on('keyup','.quantity,.tier_price',function () {
                var quantity=$(this).val();
                if (isNaN(quantity) || quantity<0) {
                    $(this).val('');
                }
            });
            $("#tiers_table").on('click','.delete-row',function () {
                $(this).parent().parent().remove();
                var tierPriceId=$(this).attr('pricelist-id');
                if (tierPriceId!='') {
                    var urlData=config.urldata;
                    jQuery("#tierprice_load").css("display","block");
                    jQuery.ajax({
                        url: urlData,
                        data: {
                            form_key: window.FORM_KEY,
                            tierPriceId:tierPriceId,
                            action: 'delete'
                            },
                        type: 'POST'
                    }).done(function (a) {
                        location.reload();
                    });
                }
                return false;
            });
            
        }
    });
    }
);
