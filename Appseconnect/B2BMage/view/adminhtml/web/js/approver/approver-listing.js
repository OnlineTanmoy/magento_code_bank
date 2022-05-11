define(
    [
    'jquery',
    'uiComponent'
    ],
    function ($, Component) {
    'use strict';
    return Component.extend({
        initialize: function (config) {
        $('#approver_addmore').click(function () {
            var text=$('#approver_listing').children('tr:last').html();
            $('#approver_listing').append('<tr>'+text+'</tr>');
        });
       $("#approver_table").on("click", ".approver-delete", function () {
            var id=$(this).attr('data');
            if (id!=undefined) {
                var data = '{"id":"'+id+'","action":"delete"}';
                var obj = eval("(" + data + ")");
                $.ajax({
                    url : config.approverordersaveurl,
                    type: 'post',
                    data:{
                          form_key: window.FORM_KEY,data:obj
                    },
                    showLoader:true,
                    success: function (data) {
                        location.reload();
                    }
                });
            }
            $(this).parent().parent().remove();
        });
       $('#approver_save').click(function () {
          $('.error-messages').remove();
            var text='';
            var iserror='';
            var checkApprover=[];
            var checkAmmount=[];
            $(".admin__control-select-appover").each(function () {
               var from= $(this).parent().parent().find('.col-discountfactor input').val();
               var to='';
               var approver=$(this).val();
               var customer_id = config.customerid;
               if (approver!='' || from!='') {
                   //validate approver
                      var fromVal=Number(from.toString().match(/^\d+(?:\.\d{0,2})?/));
                      if (checkApprover.indexOf(approver)>=0) {
                          iserror=1;
                          var message='<div class="error-messages">'
                              +'<div class="messages"><div class="message message-error error">'
                              +'<div data-ui-id="messages-message-error">'
                              +'Should not have same approver.</div></div></div></div>';
                          $('#approver_errormesage').append(message);
                          return false;
                      }
                      if (checkAmmount.indexOf(fromVal)>=0) {
                          iserror=1;
                          var message='<div class="error-messages"><div class="messages">'
                              +'<div class="message message-error error">'
                              +'<div data-ui-id="messages-message-error">'
                              +'Minimum order value should not be same.</div></div></div></div>';
                          $('#approver_errormesage').append(message);
                          return false;
                      }
                      checkApprover.push(approver);
                      checkAmmount.push(fromVal);
                    //validate approver
                     text+='{"contact_person_id":"'+approver+'" ,"customer_id":'
                        +customer_id+',"from":"'+from+'" ,"to":"'+to+'"},';
                }
               
            });
            if ($(".admin__control-select-appover-old").length && iserror=='') {
                $(".admin__control-select-appover-old").each(function () {
                       var from= $(this).parent().parent().find('.col-discountfactor input').val();
                       var to='';
                       var approver=$(this).val();
                       var insyncApproverId=$(this).attr('data');
                       var customer_id = config.customerid;
                       //validate approver
                        var fromVal=Number(from.toString().match(/^\d+(?:\.\d{0,2})?/));
                          if (checkApprover.indexOf(approver)>=0) {
                              iserror=1;
                              var message='<div class="error-messages"><div class="messages">'
                                  +'<div class="message message-error error">'
                                  +'<div data-ui-id="messages-message-error">'
                                  +'Should not have same approver.</div></div></div></div>';
                              $('#approver_errormesage').append(message);
                              return false;
                          }
                          if (checkAmmount.indexOf(fromVal)>=0) {
                              iserror=1;
                              var message='<div class="error-messages"><div class="messages">'
                                  +'<div class="message message-error error">'
                                  +'<div data-ui-id="messages-message-error">'
                                  +'Minimum order value should not be same.</div></div></div></div>';
                              $('#approver_errormesage').append(message);
                              return false;
                          }
                          checkApprover.push(approver);
                          checkAmmount.push(fromVal);
                        //validate approver
                       
                       if (approver!='' && from!='') {
                       text+='{"insync_approver_id":"'+
                       insyncApproverId+'","contact_person_id":"'+
                       approver+'","customer_id":'+
                       customer_id+','+'"from":"'+from+'" ,"to":"'+to+'"},'; }
                       
                       
                       
                    });
            }
            if (text!='' && iserror=='') {
                var data = '{"new":[' +text +'],"action":"process"}';
                var obj = eval("(" + data + ")");
                $.ajax({
                    url : config.approverordersaveurl,
                    type: 'post',
                    data:{
                        form_key: window.FORM_KEY,data:obj
                        },
                        showLoader:true,
                        success: function (data) {
                        location.reload();
                    }
                });
            }
       });
    }

    });
    }
);
