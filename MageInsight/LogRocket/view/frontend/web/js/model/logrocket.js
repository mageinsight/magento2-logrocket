define([
    'jquery',
    'underscore',
    // 'Amasty_GdprFrontendUi/js/model/cookie-data-provider',
    // 'Amasty_GdprFrontendUi/js/storage/essential-cookie',
    'Magento_Customer/js/customer-data',
    'mage/cookies'
// ], function ($, _, cookieDataProvider, essentialStorage, customerData) {
], function ($, _, customerData) {
    'use strict';

    function initialize(config) {
        window.LogRocket && window.LogRocket.init(config.appId, {
            dom: config.dom,
        });

        var customer = customerData.get('customer');
        if (customer().firstname != undefined) {
            customer.subscribe(function (data) {
                if (data.fullname != undefined) {
                    LogRocket.identify(config.customerId, {
                        website_id: data.websiteId
                    });
                }
            });
        }
    }

    /**
     * @param {Object} config
     */
    return function (config) {
        // var allowServices = false,
        //     allowedCookies,
        //     allowedWebsites,
        //     disallowedCookieAmasty,
        //     allowedCookiesAmasty,
        //     logrocketCookieName = '_lr';

        // config.cookieDomain = window.location.host;

        // if (config.isCookieRestrictionModeEnabled) {
        //     allowedCookies = $.mage.cookies.get(config.cookieName);

        //     if (allowedCookies !== null) {
        //         allowedWebsites = JSON.parse(allowedCookies);

        //         if (allowedWebsites[config.currentWebsite] === 1) {
        //             allowServices = true;
        //         }
        //     }
        // } else {
        //     allowServices = true;
        // }

        // disallowedCookieAmasty = $.mage.cookies.get('amcookie_disallowed') || '';
        // allowedCookiesAmasty = $.mage.cookies.get('amcookie_allowed') || '';
        // cookieDataProvider.getCookieData().done(function (cookieData) {
        //     essentialStorage.update(cookieData.groupData);

        //     if (((!_.contains(disallowedCookieAmasty.split(','), logrocketCookieName)
        //             && allowedCookiesAmasty) || !window.isGdprCookieEnabled
        //         || essentialStorage.isEssential(logrocketCookieName)
        //     ) && allowServices
        //     ) {
        //         initialize(config);
        //     }
        // }).fail(() => {
        //     if (allowServices && !window.isGdprCookieEnabled) {
        //         initialize(config);
        //     }
        // });
        initialize(config);
    };
});