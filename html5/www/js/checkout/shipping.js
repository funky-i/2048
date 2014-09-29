angular.module('starter.shipping', [])
    .controller('ShippingCtrl', function ($scope, $http, $location, Restangular, webStorage, Addresses, ShippingMethods, AppConfig) {
        var ShippingObj = Restangular.all('shipping');
        var checkoutState = AppConfig.checkout();
        var shipping = {};

        var shipping_address = webStorage.session.get('shipping_address_id');
        var inputData = {
            shipping_address: Addresses.get(shipping_address)
        };

        $scope.shipping_method = webStorage.session.get('shipping_method_code');

        ShippingObj.post(inputData).then(function (data) {
            $scope.shipping_methods = data.shipping_methods;
            webStorage.session.add('shipping_methods', data.shipping_methods);
        })

        $scope.setShippingMethod = function (shippingCode) {
//            console.log(ShippingMethods.get(shippingCode));
            webStorage.session.add('shipping_method_code', shippingCode);
        }

        $scope.Next = function(state) {
            $location.path(checkoutState[state]);
        }
    });
