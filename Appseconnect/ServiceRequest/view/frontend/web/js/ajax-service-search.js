define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
], function ($,alert) {
    'use strict';
    $.widget('appseconnect.AjaxServiceSearch',{
        _create: function(config,element) {
            var self = this;
            this._case = $(this.element);
            this._case.on('click', function (event) {
                var fromData=$('#from_date').val(),
                    toData=$('#to_date').val(),
                    status=$('#request_status').val()
                    self.search();
            });
        },
        search:function(){
            var fromData=$('#from_date').val(),
                toData=$('#to_date').val(),
                status=$('#request_status').val();

            var form = this.element.closest('form'),
                url = form.attr('action'),
                data = {
                    'form_key' : form.find('input[name="form_key"]').val(),
                    'from_date' : fromData,
                    'to_date' : toData,
                    'status' : status
                };
            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                beforeSend: function () {
                    $(document.body).trigger('processStart');
                },

                /** @inheritdoc */
                complete: function () {
                    $(document.body).trigger('processStop');
                }
            })
                .done(function (response) {
                    if (response.success) {
                        //this.sendQty();

                        //$('.column.main').remove();
                        //$('.order-products-toolbar.toolbar.bottom').remove();
                        //$('#authenticationPopup').after(response.content);
                        $('.request-main').html(response.content);
                        $('.column.main').trigger('contentUpdated');
                        $(".customServiceFilter").slideToggle("slow");
                        $(".filterButton").toggleClass("active");
                        $('.filterButton').unbind('click');
                        $('.filterButton').bind('click', getToggle);
                    } else {
                        //this.onError(response);
                    }
                })
        }
    });


    $('.filterButton').unbind('click');
    $('.filterButton').bind('click', getToggle);

    function getToggle() {
        $(".customServiceFilter").slideToggle("slow");
        $(this).toggleClass("active");
        return false;
    }

    return $.appseconnect.AjaxServiceSearch;

});
