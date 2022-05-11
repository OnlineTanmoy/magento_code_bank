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
       $('.action-default.scalable.category.add').click(function () {
          $('.error-messages').remove();
            
            var iserror='';
            var checkCategory=[];
            var checkAmmount=[];
            var text="";
            var update="";
            $(".category_old").each(function () {
               var status= $(this).parent().parent().find('.status').val();
               var discountfactorUp= $(this).parent().parent().find('.discountfactor_up').val();
               var discounttypeUp= $(this).parent().parent().find('.discounttype_up').val();
               var category=$(this).val();
               var categorydiscountId=$(this).attr('data');
                
               if (category!='' || discountfactorUp!='') {
                   //validet category
                      var fromVal=Number(discountfactorUp.toString().match(/^\d+(?:\.\d{0,2})?/));
                      if (checkCategory.indexOf(category)>=0) {
                          iserror=1;
                          var message='<div class="error-messages"><div class="messages">'
                              +'<div class="message message-error error">'
                              +'<div data-ui-id="messages-message-error">'
                              +'Should not have same category.</div></div></div></div>';
                          $('#approver_errormesage').append(message);
                          return false;
                      }
                      if (discountfactorUp=='' || category=='') {
                          iserror=1;
                          var message='<div class="error-messages"><div class="messages">'
                              +'<div class="message message-error error">'
                              +'<div data-ui-id="messages-message-error">'
                              +'All category and discount factor required.</div></div></div></div>';
                          $('#approver_errormesage').append(message);
                          return false;
                      }
                      checkCategory.push(category);
                      checkAmmount.push(fromVal);
                    //validet category
                     update+='{"category_id_up":"'+category+'",'
                     +'"discountfactor_up":"'+discountfactorUp+'",'
                     +'"status_up":"'+status+'",'
                     +'"discounttype_up":"'+discounttypeUp+'",'
                     +'"categorydiscount_id":"'+categorydiscountId+'" },';
                }
               
            });
            if ($(".category_new").length && iserror=='') {
                $(".category_new").each(function () {
                       var status= $(this).parent().parent().find('.status').val();
                       var discounttypeUp= $(this).parent().parent().find('.discounttype_up').val();
                       var discountfactorUp= $(this).parent().parent().find('.discountfactor_up').val();
                       var category=$(this).val();

                       //validate category
                        var fromVal=Number(discountfactorUp.toString().match(/^\d+(?:\.\d{0,2})?/));
                          if (checkCategory.indexOf(category)>=0) {
                              iserror=1;
                              var message='<div class="error-messages"><div class="messages">'
                                  +'<div class="message message-error error">'
                                  +'<div data-ui-id="messages-message-error">'
                                  +'Should not have same category.</div></div></div></div>';
                              $('#approver_errormesage').append(message);
                              return false;
                          }
                          if (discountfactorUp=='' || category=='') {
                              iserror=1;
                              var message='<div class="error-messages"><div class="messages">'
                                  +'<div class="message message-error error">'
                                  +'<div data-ui-id="messages-message-error">'
                                  +'All category and discount factor  required.</div></div></div></div>';
                              $('#approver_errormesage').append(message);
                              return false;
                          }
                          checkCategory.push(category);
                          checkAmmount.push(fromVal);
                        //validate category
                       if (category!='' && discountfactorUp!='') {
                          text+='{"category_id":"'+category+'",'
                          +'"discount_factor":"'+discountfactorUp+'",'
                          +'"discount_type":"'+discounttypeUp+'",'
                          +'"is_active":"'+status+'" },'; }
                    });
            }
            if (!$(".category_new").length && !$(".category_old").length) {
                 iserror=1;
                  var message='<div class="error-messages"><div class="messages">'
                      +'<div class="message message-error error">'
                      +'<div data-ui-id="messages-message-error">'
                      +'At least one entry is required.</div></div></div></div>';
                  $('#approver_errormesage').append(message);
                  return false;
            }
            if ((text!='' || update!='') && iserror=='') {
                $("#category_load").css("display","block");
                 var data = '{"CatagoryDetail":['
                      +text +'],'
                      +'"CatagoryDetailUpdate":['+update+']}';
                 var obj = eval("(" + data + ")");
                 var urlData=config.urldata;
                $.ajax({
                    url : urlData,
                    type: 'post',
                    data:{
                        form_key: window.FORM_KEY,data: obj,
                        cus:config.catdiscountid,
                        action: 'process'
                        },
                    showLoader:true,
                    success: function (data) {
                        location.reload();
                    }
                });
            }
       });

       $("#tiers_table").on('keyup','.discountfactor_up',function () {
        var quantity=$(this).val();
        if (isNaN(quantity) || quantity<0) {
            $(this).val('');
        }
       });
       
       $("#tiers_table").on('click','.add-more-cat-disc',function () {
           addFields();
          });
       

       var addFields = function () {

           console.log(config.categorynames);
           var resultJSON=config.categorynames;
           var category_list="";
          $.each(resultJSON, function (key, value) {
            if (key != 2 && value != 'What\'s New') {
            category_list =category_list+"<option value="+key+">"+value+"</option>";
            }
          });
           
           var count=document.getElementById("count").value;
           document.getElementById("count").value=parseInt(count)+1;
           var div = document.getElementById('listing_body');
           var table_data ='<tr>'
              +'<td class="col-category">'
              + '<select class="category_new" id="category_id'+count+'">'
              + '<option value="">Select Category</option>'+category_list+'</select></td> '
               +'<td class="col-discounttype">'
               + '<select class="discounttype_up" id="discounttype_up'+count+'">'
               + '<option value="0">By Fixed Price</option>'
               + '<option value="1">By Percentage</option>'
               + '</select></td> '
               +'<td class="col-discountfactor">'
              +'<input  class="discountfactor_up" type="text"'
              +'id="discountfactor'+count+'" value=""></td>'
              +'<td class="col-category">'
              + '<select class="status" id="status'+count+'">'
              + '<option value="1">Active</option>'
              + '<option value="0">Inactive</option>'
              + '</select></td> '
              +'  <td class="col-deletebutton"><button onclick="deleteRow(this,\'\')">Delete</button></td> '
              +'</tr>';
              $("#listing_body").append(table_data);
       }
       
       $("#tiers_table").on('click','.cat-disc-delete',function () {
           deleteRow(this);
          });
       
       var deleteRow = function (object) {
             var category_id=$(object).attr('category-id');
             $(object).parent().parent().remove();
             if (category_id!='') {
                    var urlData=config.urldata;
                    $("#category_load").css("display","block");
                    $.ajax({
                        url: urlData,
                        data: {
                            form_key: window.FORM_KEY,cod:category_id,action: 'delete',
                            cus:config.catdiscountid
                            },
                        type: 'POST'
                    }).done(function (a) {
                        location.reload();
                    });
                }
             }
       
    }
    

    
    
    });
    
    
    }
);



 
