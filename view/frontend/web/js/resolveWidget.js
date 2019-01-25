/**
 * Copyright © 2016 Resolve. All rights reserved.
 * See COPYING.txt for license details.
 */

/*jshint jquery:true*/
define([
    "jquery",
    "Resolve_Resolve/js/model/aslowas",
    "jquery/ui"
], function ($, aslowas) {

    "use strict"

    var self;
    $.widget('mage.resolveWidget', {

        /**
         * Widget options
         */
        options: {},

        /**
         * Create resolve widget
         *
         * @private
         */

        _create: function() {
            self = this;
            var priceBox = $('.price-box');
            if (typeof resolve == "undefined") {
                $.when(aslowas.loadScript(self.options)).done(function() {
                    if (priceBox.length && self.options.backorders_options !== 'undefined') {
                        priceBox.on('updatePrice', self.updatePriceHandler);
                    }
                });
            } else if (priceBox.length && self.options.backorders_options !== 'undefined') {
                priceBox.on('updatePrice', self.updatePriceHandler);
            }
        },

        /**
         * Handle update price event
         *
         * @param event
         */
        updatePriceHandler: function(event) {
            aslowas.processBackordersVisibility(self.options.backorders_options);
        }
    });

    return $.mage.resolveWidget
});