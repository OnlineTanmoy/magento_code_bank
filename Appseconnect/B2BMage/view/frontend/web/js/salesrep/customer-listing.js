define(
    [
    'jquery',
    'uiComponent'
    ],
    function ($, Component) {
    'use strict';

    return Component.extend({

        initialize: function (config) {
            var searchUrl = config.searchUrl;
            var customerViewUrl = config.customerViewUrl;
            
            $(".search-button-search.customers").click(function () {
                searchCustomers(searchUrl, customerViewUrl);
            });
            
            $('.button.reset-button.customers').click(function () {
                location.reload();
            });
            
            $('#search-customers').keypress(function (e) {
              if (e.keyCode == 13) {
                   searchCustomers(searchUrl, customerViewUrl);
                   return false;
                 }
             });
            
            
            
            
            var searchCustomers = function (searchUrl, customerViewUrl) {
                var customerName = document.getElementById("search-customers").value;
                var url=searchUrl;
                 try {
                     $.ajax({
                         url : url,
                         dataType : 'json',
                         data: { 'search_text' :  customerName  },
                         type : 'post',
                         showLoader: true,
                         success : function (data) {
                             if (!$.trim(data)) {
                                var empty =
                                 '<tr>'
                                    +'<td colspan="5" data-th="Id #">'
                                    +'<center>No result.</center></td>'
                                +'</tr>';
                                
                                 $('tbody').html(empty);
                                 $('.order-products-toolbar.toolbar.bottom').remove();
                                 }
                                 
                            var body='';
                             $.each(data, function (key, value) {
                               var url = customerViewUrl+'customer_id/'+value.entity_id;
                               var status = value.is_active ? 'Active' : 'Inactive';
                               body += '<tr>'
                                    +'<td data-th="Id #">'+value.entity_id+'</td>'
                                    +'<td data-th="Name #">'+value.name+'</td>'
                                    +'<td data-th="Email #">'+value.email+'</td>'
                                    +'<td data-th="Status #">'+status+'</td>'
                                    +'<td data-th="Actions #">'
                                    +'<a href="'+url+'"'
                                    +'" class="action order">'
                                    +'<span>Login</span>'
                                    +'</a>'
                                    +'</td>'
                                    +'</tr>';
                                
                            });
                             if (body!='') {
                                 $('tbody').html(body);
                                 $('.order-products-toolbar.toolbar.bottom').remove();
                             }
                             
                         }
                     });
                 } catch (e) {
                     console.log('failure');
                 }
            };
        },
        
        
        
        
    });
    }
);
