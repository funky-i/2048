angular.module('starter.services', [])

/**
 * A simple example service that returns some data.
 */

    .factory('ClearSession', function(Restangular, webStorage) {
        return {
            all: function() {
                webStorage.session.remove('billing_address_id');
                webStorage.session.remove('order_id');
                webStorage.session.remove('payment_method_code');
                webStorage.session.remove('products');
                webStorage.session.remove('shipping_address_id');
                webStorage.session.remove('shipping_method_code');
                webStorage.session.remove('totals');
                webStorage.session.remove('vouchers');

                webStorage.local.clear();
                webStorage.session.clear();
            },
            cart: function() {
                webStorage.session.remove('billing_address_id');
                webStorage.session.remove('order_id');
                webStorage.session.remove('payment_method_code');
                webStorage.session.remove('products');
                webStorage.session.remove('shipping_address_id');
                webStorage.session.remove('shipping_method_code');
                webStorage.session.remove('totals');
                webStorage.session.remove('vouchers');

                webStorage.session.clear();

            }
        }
    })

    .factory('Orders', function (Restangular, webStorage) {
        var OrderObj = Restangular.all('cart/products');

//        OrderObj.post().then(function (data) {
//            if (data.products != null) {
//                products = data.products;
//                vouchers = data.vouchers;
//                totals = data.totals;
//            }
//        });

        return {
            getProducts: function () {
                var products = webStorage.session.get('products');
                return products;
            },
            getVouchers: function () {
                var vouchers = webStorage.session.get('vouchers');
                return vouchers;
            },
            getTotals: function () {
                var totals = webStorage.session.get('totals');
                return totals;
            }
        }
    })

    .factory('PaymentMethods', function (webStorage) {
        return {
            all: function () {
                var payment_methods = webStorage.session.get('payment_methods');
                return payment_methods;
            },
            get: function (paymentCode) {
                var payment_methods = webStorage.session.get('payment_methods');
                return payment_methods[paymentCode];
            }
        }
    })

    .factory('AppConfig', function () {
        var url = 'http://localhost/Projects/Opencart/Present/2.0/';

        var app_config = {
            client_id: '4',
            secret: 'abc',
            grant_type: 'password',
            baseURL: url + 'index.php?route=',
            imageURL: url + 'image/'
        };

        var payment_callback = {
            return: 'http://localhost/#/tab/cart/complete',
            notify_url: url + 'index.php?route=api/payment/callback',
            cancel_return: 'http://localhost/#/tab/cart/checkout'
        };

        var checkout_step = {
            0: 'tab/cart',
            1: 'tab/order/billing',
            2: 'tab/order/shipping',
            3: 'tab/order/shippingmethod',
            4: 'tab/order/paymentmethod',
            5: 'tab/order/confirm'
        };

        return {
            apps: function () {
                return app_config;
            },
            checkout: function () {
                return checkout_step;
            },
            payment_callback: function () {
                return payment_callback;
            }
        }

    })

    .factory('DeliveryMethods', function (webStorage) {
        return {
            all: function () {
                var delivery_methods = webStorage.session.get('delivery_methods');
                return delivery_methods;
            },
            get: function (shippingCode) {
                var delivery_methods = webStorage.session.get('delivery_methods');
                return delivery_methods[shippingCode].quote[shippingCode];
            }
        }
    })

    .factory('Addresses', function (webStorage) {
        return {
            all: function () {
                var addresses = webStorage.session.get('addresses');
                return addresses;
            },
            get: function (addressId) {
                var addresses = webStorage.session.get('addresses');
                return addresses[addressId];
            }
        }
    })

    .factory('Products', function ($http) {
        var products = {};
        var status = false;
        var url = AppConfig.app_config.baseURL + 'api/product';

        return {
            all: function () {

                products = $http.get(url).then(function (response) {
                    return response.data;
                });

//                $http({method: 'GET', url: url})
//                .success(function(data, status, headers, config) {
////                        console.log(data);
////                    $scope.products = data;
//                })
//                .error(function(data, status, headers, config) {
//                    return false;
//                });

                return products;
            }

        }
    })

    .factory('Friends', function () {
        // Might use a resource here that returns a JSON array

        // Some fake testing data
        var friends = [
            { id: 0, name: 'Scruff McGruff' },
            { id: 1, name: 'G.I. Joe' },
            { id: 2, name: 'Miss Frizzle' },
            { id: 3, name: 'Ash Ketchum' }
        ];

        return {
            all: function () {
                return friends;
            },
            get: function (friendId) {
                // Simple index lookup
                return friends[friendId];
            }
        }
    });
