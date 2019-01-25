/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category    Resolve
 * @package     Resolve_Resolve
 * @copyright   Copyright (c) 2014 One Pica, Inc. (http://www.onepica.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Ui/js/model/messages',
        'Magento_Checkout/js/action/set-payment-information',
        'Resolve_Resolve/js/action/prepare-resolve-checkout',
        'Resolve_Resolve/js/action/send-to-resolve-checkout',
        'Resolve_Resolve/js/action/verify-resolve'
    ],
    function ($, Component, quote, additionalValidators,
              urlBuilder, errorProcessor, Messages, setPaymentAction,
              initChekoutAction, sendToResolveCheckout, verifyResolveAction) {

        'use strict';

        return Component.extend({
            defaults: {
                template: 'Resolve_Resolve/payment/form',
                transactionResult: ''
            },

            /**
             * Init Resolve specify message controller
             */
            initResolve: function() {
                this.messageContainer = new Messages();
            },

            /**
             * Payment code
             *
             * @returns {string}
             */
            getCode: function () {
                return 'resolve_gateway';
            },

            /**
             * Get payment info
             *
             * @returns {info|*|indicators.info|z.info|Wd.$get.info|logLevel.info}
             */
            getInfo: function () {
                return window.checkoutConfig.payment['resolve_gateway'].info
            },

            /**
             * Get resolve logo src from config
             *
             * @returns {*}
             */
            getResolveLogoSrc: function () {
                return window.checkoutConfig.payment['resolve_gateway'].logoSrc;
            },

            /**
             * Get visible
             *
             * @returns {*}
             */
            getVisibleType: function() {
                return window.checkoutConfig.payment['resolve_gateway'].visibleType;
            },

            getDescription: function () {
                return window.checkoutConfig.payment['resolve_gateway'].description;
            },

            getVisibleTypeDescription: function() {
                return window.checkoutConfig.payment['resolve_gateway'].visibleTypeDescription;
            },

            /**
             * Continue to Resolve redirect logic
             */
            continueInResolve: function() {
                var self = this;
                if (additionalValidators.validate()) {
                    //update payment method information if additional data was changed
                    this.selectPaymentMethod();
                    $.when(setPaymentAction(self.messageContainer, {'method': self.getCode()})).done(function() {
                        $.when(initChekoutAction(self.messageContainer)).done(function(response) {
                            sendToResolveCheckout(response);
                        });
                    }).fail(function(){
                        self.isPlaceOrderActionAllowed(true);
                    });
                    return false;
                }
            },

            /**
             * Init payment
             */
            initialize: function () {
                var _self = this;
                this._super();
                $.when(verifyResolveAction(_self.messageContainer)).done(function(response){
                    if (response) {
                       _self.selectPaymentMethod();
                    }
                }).fail(function(response){
                    errorProcessor.process(response, _self.messageContainer);
                });
            }
        });
    }
);
