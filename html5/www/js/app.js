// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'starter.services' is found in services.js
// 'starter.controllers' is found in controllers.js
angular.module('starter', ['ionic',
    'restangular', 'ngCookies', 'ngRoute', 'ngStorage', 'LocalStorageModule', 'webStorageModule',
    'starter.controllers', 'starter.services', 'starter.product', 'starter.account', 'starter.cart', 'starter.order',
    'starter.checkout', 'starter.address', 'starter.shipping', 'starter.payment',
    'starter.search', 'starter.dummy',
    'cod', 'paypal', 'paysbuy'])

    .constant('$ionicLoadingConfig', {
        template: 'Page loading...',
        duration: 5000
    })

//    .run(function ($ionicPlatform) {
//        $ionicPlatform.ready(function () {
//            // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
//            // for form inputs)
//            if (window.cordova && window.cordova.plugins.Keyboard) {
//                cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
//            }
//            if (window.StatusBar) {
//                // org.apache.cordova.statusbar required
//                StatusBar.styleDefault();
//            }
//        });
//    })

    .config(function ($stateProvider, $urlRouterProvider, RestangularProvider, $routeProvider) {

        var index = 'index.php?route=api';
        RestangularProvider.setBaseUrl('http://192.168.1.35/Projects/Opencart/Present/2.0/' + index);
        RestangularProvider.setDefaultHeaders({
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        });
        RestangularProvider.setDefaultHttpFields({
            'withCredentials': true
        });

        // Ionic uses AngularUI Router which uses the concept of states
        // Learn more here: https://github.com/angular-ui/ui-router
        // Set up the various states which the app can be in.
        // Each state's controller can be found in controllers.js
        $stateProvider

            // setup an abstract state for the tabs directive
            .state('tab', {
                url: "/tab",
                abstract: true,
                templateUrl: "templates/tabs.html"
            })

            // Each tab has its own nav history stack:

            .state('tab.dash', {
                url: '/dash',
                views: {
                    'tab-dash': {
                        templateUrl: 'templates/tab-dash.html',
                        controller: 'DashCtrl'
                    }
                }
            })

            .state('tab.products', {
                url: '/product',
                views: {
                    'tab-product': {
                        templateUrl: 'templates/tab-products.html',
                        controller: 'ProductCtrl'
                    }
                }
            })

            .state('tab.product-detail', {
                url: '/product/:productId',
                views: {
                    'tab-product': {
                        templateUrl: 'templates/product/product-detail.html',
                        controller: 'ProductDetailCtrl'
                    }
                }
            })

            .state('tab.search', {
                url: '/search',
                views: {
                    'tab-search': {
                        templateUrl: 'templates/tab-search.html',
                        controller: 'SearchCtrl'
                    }
                }
            })

            .state('tab.cart', {
                url: '/cart',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/tab-cart.html',
                        controller: 'OrderCtrl'
                    }
                }
            })

            // Checkout
            .state('tab.billing', {
                url: '/order/billing',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/checkout/payment.html',
                        controller: 'AddressCtrl'
                    }
                }
            })
            .state('tab.shipping', {
                url: '/order/shipping',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/checkout/shipping.html',
                        controller: 'AddressCtrl'
                    }
                }
            })
            .state('tab.shippingmethod', {
                url: '/order/shippingmethod',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/checkout/shipping_method.html',
                        controller: 'ShippingCtrl'
                    }
                }
            })
            .state('tab.paymentmethod', {
                url: '/order/paymentmethod',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/checkout/payment_method.html',
                        controller: 'PaymentCtrl'
                    }
                }
            })
            .state('tab.confirm', {
                url: '/order/confirm',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/checkout/confirm.html',
                        controller: 'CheckoutCtrl'
                    }
                }
            })
            .state('tab.complete', {
                url: '/order/complete',
                views: {
                    'tab-cart': {
                        templateUrl: 'templates/checkout/complete.html',
                        controller: 'CompleteCtrl'
                    }
                }
            })

            .state('cod', {
                url: "/payment/cod/:OrderId/:PaymentCode",
                templateUrl: 'templates/payment/cod.html',
                controller: 'CODCtrl'
            })
            .state('paypal', {
                url: "/payment/pp_standard/:OrderId/:PaymentCode",
                templateUrl: 'templates/payment/pp_standard.html',
                controller: 'PaypalCtrl'
            })
            .state('paysbuy', {
                url: "/payment/paysbuy/:OrderId/:PaymentCode",
                templateUrl: 'templates/payment/paysbuy.html',
                controller: 'PaySbuyCtrl'
            })



//      .state('tab.information', {
//          url: '/information',
//          views: {
//              'tab-information': {
//                  templateUrl: 'templates/tab-information.html',
//                  controller: ''
//              }
//          }
//      })

            .state('tab.account', {
                url: '/account',
                views: {
                    'tab-account': {
                        templateUrl: 'templates/tab-account.html',
                        controller: 'AccountCtrl'
                    }
                }
            })

            .state('tab.transaction', {
                url: "/account/transaction",
                views: {
                    'tab-account': {
                        templateUrl: 'templates/account/transaction.html',
                        controller: 'AccountCtrl'
                    }
                }
            })
            .state('tab.register', {
                url: "/account/registration",
                views: {
                    'tab-account': {
                        templateUrl: 'templates/account/register.html',
                        controller: 'AccountCtrl'
                    }
                }
            })

        // if none of the above states are matched, use this as the fallback
        $urlRouterProvider.otherwise('/tab/dash');

    });

