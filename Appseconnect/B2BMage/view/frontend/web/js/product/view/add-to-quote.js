/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    "use strict";

    $.widget('mage.catalogAddToQuote', {

        options: {
            processStart: null,
            processStop: null,
            addToQuoteButtonSelector: '.action.toquote',
            qty: null,
            formData: null,
            actionUrl: null,
            productId: null,
            productPrice: null,
            addToQuoteButtonDisabledClass: 'disabled',
            bindSubmit: true,
            addToQuoteButtonTextWhileAdding: '',
            addToQuoteButtonTextAdded: '',
            addToQuoteButtonTextDefault: ''
        },

        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },
        
        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },
        
        _bindSubmit: function () {
            var self = this;
            this.element.on('click', this.options.addToQuoteButtonSelector, function (e) {
                self.submitForm($(this));
            });
        },
        
        submitForm : function (form) {
            this.options.qty = $(form).parent().parent().find('.input-text.qty').val();
            this.options.formData = $(form).parent().parent().parent().parent().parent().parent().find('#product_addtocart_form');
            
            var addToQuoteButton, self = this;

            //if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                // disable 'Add to Cart' button
                addToQuoteButton = $(form).parent().parent().find(this.options.addToQuoteButtonSelector);
                addToQuoteButton.prop('disabled', true);
                addToQuoteButton.addClass(this.options.addToQuoteButtonDisabledClass);
                self.ajaxSubmit(this.options.formData);
            /*} else {
                self.ajaxSubmit(form);
            }*/
        },
        
        ajaxSubmit: function (form) {
            var self = this;
            self.disableAddToQuoteButton(form);
            
            $.ajax({
                url: this.options.actionUrl,
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (res) {
                    /*if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }*/
                    self.enableAddToQuoteButton(form);
                }
            });
            
            
        },
        
        disableAddToQuoteButton: function (form) {
            var addToQuoteButtonTextWhileAdding = this.options.addToQuoteButtonTextWhileAdding || $t('Adding...');
            var addToQuoteButton = $(form).parent().parent().find(this.options.addToQuoteButtonSelector);
            addToQuoteButton.addClass(this.options.addToQuoteButtonDisabledClass);
            addToQuoteButton.find('span').text(addToQuoteButtonTextWhileAdding);
            addToQuoteButton.attr('title', addToQuoteButtonTextWhileAdding);
        },
        
        enableAddToQuoteButton: function (form) {
            var addToQuoteButtonTextAdded = this.options.addToQuoteButtonTextAdded || $t('Added');
            var self = this,
                addToQuoteButton = $(form).parent().parent().find(this.options.addToQuoteButtonSelector);

            addToQuoteButton.find('span').text(addToQuoteButtonTextAdded);
            addToQuoteButton.attr('title', addToQuoteButtonTextAdded);

            setTimeout(function () {
                var addToQuoteButtonTextDefault = self.options.addToQuoteButtonTextDefault || $t('Add to Quote');
                addToQuoteButton.removeClass(self.options.addToQuoteButtonDisabledClass);
                addToQuoteButton.prop('disabled', false);
                addToQuoteButton.find('span').text(addToQuoteButtonTextDefault);
                addToQuoteButton.attr('title', addToQuoteButtonTextDefault);
            }, 1000);
        }
        
        
        
        
    });

    return $.mage.catalogAddToQuote;
});

