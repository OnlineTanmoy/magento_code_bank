require(["jquery", "mage/mage"], function(jQuery){
        jQuery('#reorder-validate-detail').mage('validation', {
            errorPlacement: function(error, element) {
                error.appendTo('#cart-sidebar-reorder-advice-container');
            }
        });
    });