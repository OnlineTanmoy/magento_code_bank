define([
    'jquery',
    'mage/translate',
    'mage/mage',
    'jquery/ui',
    'Appseconnect_ServiceRequest/js/select2.min',
    'mage/template',
    'Magento_Ui/js/modal/modal',
    'mage/validation'
], function($,tr,mage){
    var url = BASE_URL  + 'servicerequest/request/description';

    var dataForm = $('#service-request');
    dataForm.mage('validation', {});

    $('button#submit_request').click( function() { //can be replaced with any event
        var status = dataForm.validation('isValid'); //validates form and returns boolean
        if(status){
            if(!confirm($.mage.__("You cannot change the Service Request after you submit it. Confirm ?"))) {
                return false;
            } else {
                $('button#submit_request').attr("disabled", "disabled");
                $('button#draft_request').attr("disabled", "disabled");
                dataForm.submit();
            }

        }
    });
    $('button#draft_request').click( function() {
        $('button#draft_request').attr("disabled", "disabled");
        $('button#submit_request').attr("disabled", "disabled");
    });
    $(".select.frontend__control-select.serviceproduct").select2({
        placeholder: $.mage.__('Search With Part No./SKU #'),
        ajax: {
            url: url,
            dataType: 'json',
            delay: 1200,
            data: function (params) {
                return {
                    productModel: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: false
        },
        minimumInputLength: 3
    });


    $('.select.frontend__control-select.productadd').on('select2:open', function (e) {
        setInterval(function() {
            if ($('.select2-results__option.select2-results__message').length) {
                $(".select2-results__option.select2-results__message").text(function (index, text) {
                    return text.replace('Please enter 3 or more characters', $.mage.__('Please enter 3 or more characters'));
                });
                clearInterval(checkExist);
            }
        }, 10);
    });


    $(document).ready(function() {
        if(tooltip_model_no != '') {
            $('#tooltip-model-no').click(function(){
                $(".img-popup").html(tooltip_model_no);
                $(".img-popup").modal(options).modal("openModal");
            });
        }
        if(tooltip_serial_no != '') {
            $('#tooltip-serial-no').click(function(){
                $(".img-popup").html(tooltip_serial_no);
                $(".img-popup").modal(options).modal("openModal");
            });
        }

        $('.device-type input:radio').on({
            "click": function() {
                var tooltipVal= '<img width="1200" src="'+$(this).parent().find('.device_type_image').val()+'"/>';
                tooltip_model_no = tooltipVal;
                tooltip_serial_no = tooltipVal;
                tooltip_copack_no = tooltipVal;
                $('#tooltip-model-no').click(function(){
                    $(".img-popup").html(tooltip_model_no);
                    $(".img-popup").modal(options).modal("openModal");
                });
                $('#tooltip-copack-no').click(function(){
                    $(".img-popup").html(tooltip_serial_no);
                    $(".img-popup").modal(options).modal("openModal");
                });
                $('#tooltip-serial-no').click(function(){
                    $(".img-popup").html(tooltip_serial_no);
                    $(".img-popup").modal(options).modal("openModal");
                });
            }
        });

        var checkExist = setInterval(function() {
            if ($('.select2-results__option.select2-results__message').length) {
                $(".select2-results__option.select2-results__message").text(function (index, text) {
                    return text.replace('Please enter 3 or more characters', $.mage.__('Please enter 3 or more characters'));
                });
                clearInterval(checkExist);
            }
        }, 100);
    });

    $('#device-details').on('change', '.select.frontend__control-select.serviceproduct', function () {
        var data = $(this).val();
        var obj = $.parseJSON(data);
        $("#product_description").val(obj.description);
        $('#serial_number').focus();
    });


    //Serial Number

    $("#serial_number").keyup(function (e) {
        if (e.which != 13) {
            var serial = $(this).val();
            var model = $('#product_model').val();

            //alert(serial.length);
            if (serial.length >= 3) {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data:{
                        productSerial: serial
                    },
                    success: function (data) {
                        if (data.isRequired == 1) {
                            $('#serial-error').html('');
                            $('.co-pack-feild').show();
                            $('#copack_number-error').html('This is a required field.');
                            $('#copack_number-error').css('display','block');
                            $("#copack_serial_number").prop('required', true);

                        } else if (data.isRequired == 2) {
                            $('#serial-error').html('');
                            $('.co-pack-feild').hide();
                            $('#copack_number-error').css('display','none');
                            $("#copack_serial_number").prop('required', false);
                        }
                    }
                });
            }
        }
    });

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

    $("#valid_device").click(function(e){

        var model = $('#product_model').val();
        var srl = $('#serial_number').val();

        if(srl == "" || model == ""){
            alert('Provide the required info.');
        }else {
            var obj = $.parseJSON(model);
            model = obj.model_number;

            var copak = $('#copack_serial_number').val();
            if($('#copack_serial_number').prop('required') && copak == ""){
                $('#copack_number-error').css('display','block');
                $('#copack_serial_number').focus();
                return false;
            }
            $('#copack_number-error').css('display','none');
            $.ajax({
                type: "POST",
                url: url,
                showLoader: true,
                data:
                    {
                        'product_model': model,
                        'serial_number': srl,
                        'copack_serial_number': copak
                    },
                success: function (data) {
                    var obj = $.parseJSON(data);
                    if (obj.isValid) {
                        $("#report_fault_details").show();
                        $("#safety1").prop("disabled", false);
                        $("#safety12").prop("disabled", false);
                        $("#safety2").prop("disabled", false);
                        $("#safety22").prop("disabled", false);
                        $("#safety3").prop("disabled", false);
                        $("#safety32").prop("disabled", false);

                        if(obj.isEnded){
                            $(".warranty-product-file").css('display', 'none');
                        }else{
                            $(".warranty-product-file").show();
                        }
                        $('#report-fault-details').css('display', 'block');
                        $('.total-error-msg').html('');
                    }else{
                        if(obj.isEnded){
                            $(".warranty-product-file").css('display', 'none');
                        }else{
                            $(".warranty-product-file").show();
                        }
                        //$('#copack_number-error').css('display','block');
                        $('#report-fault-details').css('display', 'none');
                        $('.total-error-msg').html(obj.message);
                    }
                }
            });
        }
    });

    $('#file_path').bind('change', function() {
        var ext = $('#file_path').val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'jpeg', 'jpg', 'png']) == -1){
            $('#file_path_error1').slideDown("slow");
            $('#file_path_error2').slideUp("slow");
            $('#file_path').val('');
        }else{
            var picsize = (this.files[0].size);
            if (picsize > 5000000){
                $('#file_path_error2').slideDown("slow");
                $('#file_path').val('');
            }else{
                $('#file_path_error2').slideUp("slow");
            }
            $('#file_path_error1').slideUp("slow");
        }
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
        if ($('input[name="safety1"]:checked').length == 0){
            $('#safety1message').html('This is a required field');
        }
        if ($('input[name="safety2"]:checked').length == 0){
            $('#safety2message').html('This is a required field');
        }
        if ($('input[name="safety3"]:checked').length == 0){
            $('#safety3message').html('This is a required field');
        }
        if ($('input[name="terms_condition"]:checked').length == 0){
            $('#terms_conditionmessage').html('This is a required field');
        }
        if ($('#short_description').val() == ''){
            $('#short_descriptionmessage').html('This is a required field');
        }
        if ($('input[name="safety1"]:checked').length != 0 && $('input[name="safety2"]:checked').length != 0 && $('input[name="safety3"]:checked').length != 0 && $('input[name="terms_condition"]:checked').length != 0 && $('#short_description').val() != '') {
            return true;
        }
        return false;
    });

});
