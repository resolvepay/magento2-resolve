/**
 * Copyright © 2015 Fastgento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint jquery:true*/
define([
    "jquery",
    "mage/translate",
    "Magento_Checkout/js/model/full-screen-loader",
    "Magento_Checkout/js/model/quote",
    "mage/url",
    'Magento_Customer/js/model/customer',
    "Resolve_Resolve/js/model/resolve",
    'Magento_Ui/js/model/messageList'
], function ($, $t, fullScreenLoader, quote, url, customer, resolveCheckout, Messages) {

    return function(response) {
        fullScreenLoader.startLoader();
        var result = JSON.parse(response),
            giftWrapItems = result.wrapped_items,
            checkoutObj;

        resolveCheckout.prepareOrderData(result);
        if (typeof giftWrapItems !== 'undefined') {
            resolveCheckout.addItems(giftWrapItems);
        }
        try {
            checkoutObj = resolveCheckout.getData();

            resolveCheckout.checkout(Object.assign({},result , checkoutObj));
        } catch (err) {
            Messages.addErrorMessage({
                    'message': $t('We have a problem with your resolve script loading, please verify your API URL!')}
            );
            fullScreenLoader.stopLoader();
        }
    }
});
