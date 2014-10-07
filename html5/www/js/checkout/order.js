angular.module('starter.order', [])
    .controller('OrderCtrl', function ($scope, $http, $location, Restangular, Orders, webStorage, AppConfig) {
        var OrderObj = Restangular.all('cart/products');
        var checkoutState = AppConfig.checkout();

        OrderObj.post().then(function (data) {
            if (data.products!=null) {
                OrderCallback(data);
            }
        });

        OrderCallback = function (data) {
//            console.log('OrderCtrl');
//            console.log(data);

            $scope.products = data.products;
            $scope.vouchers = data.vouchers;
            $scope.totals = data.totals;

            webStorage.session.add('products', data.products);
            webStorage.session.add('vouchers', data.vouchers);
            webStorage.session.add('totals', data.totals);

        }

        $scope.Next = function (state) {
            $location.path(checkoutState[state]);
        }

        $scope.Clear = function () {
            Restangular.all('cart/clear').post().then(function (data) {
//                console.log(data);
                $scope.products = {};
                $scope.vouchers = {};
                $scope.totals = data.totals;
            })
        }


    })
;