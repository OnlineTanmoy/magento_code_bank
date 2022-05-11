define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
], function ($,alert) {
    'use strict';
    $.widget('appseconnect.RegisterProduct',{
        _create: function(config,element) {
            var self = this;
            this._case = $(this.element);
            this._case.on('click', function (event) {
                var sku=$('#sku').val(),
                    serialNumber=$('#serial_number').val()
                self.search();
            });
        },
        search:function(){
            var sku=$('#sku').val(),
                serialNumber=$('#serial_number').val();

            var form = this.element.closest('form'),
                url = form.attr('action'),
                data = {
                    'form_key' : form.find('input[name="form_key"]').val(),
                    'sku' : sku,
                    'serial_number' : serialNumber
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

                        //$('.registerpro-main').remove();
                        //$('.order-products-toolbar.toolbar.bottom').remove();
                        //$('#authenticationPopup').after(response.content);
                        $('.registerpro-main').html(response.content);
                        $('.column.main').trigger('contentUpdated');
                        $(".customRegisterFilter").slideToggle("slow");
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
        $(".customRegisterFilter").slideToggle("slow");
        $(this).toggleClass("active");
        return false;
    }

    var url = BASE_URL  + 'servicerequest/request/description';

    var dataForm = $('#new-register-request');
    dataForm.mage('validation', {});

    $('button#submit_request').click( function() { //can be replaced with any event
        var status = dataForm.validation('isValid'); //validates form and returns boolean
        if(status){
            $('button#submit_request').attr("disabled", "disabled");
            dataForm.submit();
        }
    });

    $('#device-details').on('change', '.select.frontend__control-select.serviceproduct', function () {
        var data = $(this).val();
        var obj = $.parseJSON(data);
        $("#product_description").val(obj.description);
        $('#serial_number').focus();
    });


    //Serial Number

    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        modalClass: 'see-detail-modal',
        buttons: [{
            text: $.mage.__('Close'),
            class: 'action secondary action-hide-popup',
            click: function () {
                this.closeModal();
            }
        }]
    };

    $('.terms-condition label').click(function(){
        $(".terms-condition-div").modal(options).modal("openModal");
    });

    $('#purchase_order_file').bind('change', function() {
        var ext = $('#purchase_order_file').val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'jpeg', 'jpg', 'png']) == -1){
            $('#purchase_order_file_path_error1').slideDown("slow");
            $('#purchase_order_file_path_error2').slideUp("slow");
            $('#purchase_order_file').val('');
        }else{
            var picsize = (this.files[0].size);
            if (picsize > 5000000){
                $('#purchase_order_file_path_error2').slideDown("slow");
                $('#purchase_order_file').val('');
            }else{
                $('#purchase_order_file_path_error2').slideUp("slow");
            }
            $('#purchase_order_file_path_error1').slideUp("slow");
        }
    });

    $('#submit_request').click(function(){
        if ($('input[name="terms_condition"]:checked').length == 0){
            $('#terms_condition_msg').html('This is a required field');
        }
        if ($('#serial_number').val() == ''){
            $('#serial_msg').html('This is a required field');
        }
        if ($('#model_number').val() == ''){
            $('#model_msg').html('This is a required field');
        }
        if ($('#date_of_purchase').val() == ''){
            $('#date_of_purchase_msg').html('This is a required field');
        }
        if ($('#purchase_order_number').val() == ''){
            $('#purchase_order_number_msg').html('This is a required field');
        }
        if ($('#purchase_order_file').val() == ''){
            $('#purchase_order_file_msg').html('This is a required field');
        }
        if ($('#short_description').val() == ''){
            $('#short_descriptionmessage').html('This is a required field');
        }
        return false;
    });


    $(document).ready(function() {
        $('#tooltip-model-no').click(function(){
            $(".img-popup").html(tooltip_model_no);
            $(".img-popup").modal(options).modal("openModal");
        });
        $('#tooltip-serial-no').click(function(){
            $(".img-popup").html(tooltip_serial_no);
            $(".img-popup").modal(options).modal("openModal");
        });
    });

    return $.appseconnect.RegisterProduct;

});
