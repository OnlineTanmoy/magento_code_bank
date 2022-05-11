require([
    'jquery'
], function ($) {
    'use strict';

    $(window).on("load", function () {
        var customerId = $("#page_customer_id").val();
        var url = window.specialpriceUrl;
        getPricelistName(customerId, url);
    });

    $('#page_customer_id').on('change', function() {
        var customerId = this.value;
        var url = window.specialpriceUrl;
        getPricelistName(customerId, url);
    });

    function getPricelistName(customerId, url) {

        if (customerId > 0) {
            $.ajax({
                showLoader: true,
                url: url,
                data: {form_key: window.FORM_KEY, 'customer_id':customerId},
                type: "POST",
                cache: false,
                dataType: 'json'

            }).done(function (response) {
                var pricelistId = response.pricelistId;
                var pricelistName = response.pricelistName;

                $("#page_pricelist_name").val(pricelistName);
                $("#page_pricelist_id").val(pricelistId);
            });
        }
    }

});