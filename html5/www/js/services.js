angular.module('starter.services', [])

/**
 * A simple example service that returns some data.
 */

    .factory('AppConfig', function () {
        var app_config = {
            client_id: '4',
            secret: 'abc',
            grant_type: 'password',
            baseURL: 'http://localhost/Projects/Opencart/Present/2.0/index.php?route=',
            imageURL: 'http://localhost/Projects/Opencart/Present/2.0/image/'
        };
        var checkout_step = {
            1 : 'tab/order/billing',
            2 : 'tab/order/shipping',
            3 : 'tab/order/shippingmethod',
            4 : 'tab/order/paymentmethod',
            5 : 'tab/order/confirm'
        };

        return {
            apps: function() {
                return app_config;
            },
            checkout: function() {
                return checkout_step;
            }
        }

    })

    .factory('Products', function ($http) {
        var products = {};
        var status = false;
        var url = 'http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=api/product';

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
