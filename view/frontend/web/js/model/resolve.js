/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    "jquery",
    "underscore",
    "Magento_Checkout/js/model/quote",
    "mage/url",
    'Magento_Customer/js/model/customer'
], function ($, _, quote, url, customer) {
    'use strict';
    var configData = window.checkoutConfig.payment['resolve_gateway'];
    var DOMAIN = 'paywithresolve.com';
    var HOSTS = {
        development: 'http://localhost:2222',
        sandbox: 'https://apply-sandbox.' + DOMAIN,
        production: 'https://apply.' + DOMAIN
    };
    return {
        items: [],
        shipping: null,
        billing: null,
        config: configData.config,
        merchant: configData.merchant,
        mode: configData.mode,
        discounts: null,
        orderId: null,
        shippingAmount: null,
        tax_amount: null,
        total: null,

        /**
         * Get checkout data
         *
         * @returns {{merchant: (*|resolve2_ee_new.pub.static.frontend.Magento.blank.en_US.Resolve_Resolve.js.model.resolve.merchant|resolve2_ee_new.pub.static.frontend.Magento.blank.en_US.Resolve_Resolve.js.model.resolve.getData.merchant|resolve2_ee_new.pub.static.frontend.Magento.luma.en_US.Resolve_Resolve.js.model.resolve.merchant|resolve2_ee_new.pub.static.frontend.Magento.luma.en_US.Resolve_Resolve.js.model.resolve.getData.merchant|checkout.merchant), config: (*|resolve2_ee_new.pub.static.frontend.Magento.luma.en_US.Magento_Shipping.js.view.checkout.shipping.shipping-policy.config|resolve2_ee_new.vendor.magento.module-shipping.view.frontend.web.js.view.checkout.shipping.shipping-policy.config|resolve2_ee_new.pub.static.frontend.Magento.blank.en_US.Magento_Shipping.js.view.checkout.shipping.shipping-policy.config|exports.file.options.config|exports.test.options.config), items: *, order_id: *, shipping_amount: (null|resolve2_ee_new.pub.static.frontend.Magento.luma.en_US.Resolve_Resolve.js.model.resolve.shippingAmount|resolve2_ee_new.pub.static.frontend.Magento.blank.en_US.Resolve_Resolve.js.model.resolve.shippingAmount|number)}}
         */
        getData: function() {
            var _self = this;
            this.prepareItems();
            this.prepareTotals();
            this.initMetadata();
            return {
                merchant: _self.merchant,
                sandbox: _self.mode === 'sandbox',
                modal: true
            }
        },

        /**
         * Prepare items data
         */
        prepareItems: function() {
            var quoteItems = quote.getItems();
            for (var i=0; i < quoteItems.length; i++) {
                this.items.push({
                    display_name : quoteItems[i].name,
                    sku : quoteItems[i].sku,
                    unit_price : parseInt(quoteItems[i].price * 100),
                    qty : quoteItems[i].qty,
                    item_image_url : quoteItems[i].thumbnail,
                    item_url : (quoteItems[i].product.request_path) ?
                        url.build(quoteItems[i].product.request_path) : quoteItems[i].thumbnail
                });
            }
        },

        /**
         * Init metadata
         */
        initMetadata: function() {
            if (!this.metadata.shipping_type && quote.shippingMethod()) {
                this.metadata.shipping_type =
                    quote.shippingMethod().carrier_title + ' - ' + quote.shippingMethod().method_title;
            }
        },

        /**
         * Set order id
         *
         * @param orderId
         */
        setOrderId: function(orderId) {
            if (orderId) {
                this.orderId = orderId;
            }
        },

        /**
         * Prepare totals data
         */
        prepareTotals: function() {
            var totals = quote.getTotals()();
            this.shippingAmount = this.convertPriceToCents(totals.base_shipping_amount);
            this.total = this.convertPriceToCents(totals.base_grand_total);
            this.tax_amount = this.convertPriceToCents(totals.base_tax_amount);
        },

        /**
         * Convert price to cents
         *
         * @param price
         * @returns {*}
         */
        convertPriceToCents: function(price) {
            if (price && price > 0) {
                price = Math.round(price*100);
                return price;
            }
            return 0;
        },

        /**
         * Prepare address data
         *
         * @param type
         * @returns {{}}
         */
        prepareAddress: function(type) {
            var name, address, fullname, street, result = {};
            if (type == 'shipping') {
                address = quote.shippingAddress();
            } else if (type == 'billing') {
                address = quote.billingAddress();
            }
            if (address.lastname) {
                fullname = address.firstname + ' ' + address.lastname;
            } else {
                fullname = address.firstname;
            }
            name = {
                "full": fullname
            };
            if (address.street[0]) {
                street = address.street[0];
            }
            result["address"] = {
                "line1": street,
                "city": address.city,
                "state": address.regionCode,
                "zipcode": address.postcode,
                "country": address.countryId
             };
            result["name"] = name;
            if (address.street[1]) {
                result.address.line2 = address.street[1];
            }
            if (address.telephone) {
                result.phone_number = address.telephone;
            }
            if (!customer.isLoggedIn()) {
                result.email = quote.guestEmail;
            } else if (customer.customerData.email) {
                result.email = customer.customerData.email;
            }
            return result;
        },

        /**
         * Specify order Data
         *
         * @param data
         */
        prepareOrderData: function(data) {
            if (data.order_increment_id !== 'undefined') {
                this.order_id = data.order_increment_id;
            }
            if (data.discounts) {
                this.setDiscounts(data.discounts);
            }
            if (data.metadata) {
                this.setMetadata(data.metadata);
            }
            if (data.financing_program) {
                this.setFinancingProgram(data.financing_program);
            }
        },

        /**
         * Add items
         *
         * @param items
         */
        addItems: function (items) {
            if (items !== 'undefined') {
                this.items = _.union(this.items, items);
            }
        },

        /**
         * Specify discount
         *
         * @param discounts
         */
        setDiscounts: function(discounts) {
            if (discounts) {
                this.discounts = discounts;
            }
        },

        /**
         * Specify metadata
         *
         * @param metadata
         */
        setMetadata: function(metadata) {
            if (metadata) {
                this.metadata = metadata;
            }
        },

        /**
         * Specify financing program
         *
         * @param financing_program
         */
        setFinancingProgram: function(financing_program) {
            if (financing_program) {
                this.financing_program = financing_program;
            }
        },

        /**
         * Get specified financing program
         *
         * @return string
         */
        getFinancingProgram: function() {
            return this.financing_program;
        },

        checkout: function checkout(data) {
            var host = this.getHost(data);

            $.ajax({
                url: host + '/api/checkouts',
                method: "POST",
                data: {
                    'data' : JSON.stringify(data)
                }
            }).done(function(response) {
                window.location.href = host + '/checkout/' + response.id;
            });
        },

        getHost: function getHost(data) {
            var env = 'production';
            if (data.sandbox === true) {
                env = 'sandbox';
            } else if (data.development) {
                env = 'development';
            }
            if (!HOSTS[env]) {
                throw new Error('Invalid env specified: ' + env);
            }
            return HOSTS[env];
        }
    }
});
