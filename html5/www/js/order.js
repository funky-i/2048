angular.module('starter.order', [])
    .controller('OrderCtrl', function ($scope, $http, Restangular, webStorage, AppConfig) {
        var OrderObj = Restangular.all('cart/products');

        OrderObj.post().then(function (data) {
            if (data.products!=null) {
                OrderCallback(data);
            }
        });

        OrderCallback = function (data) {
            $scope.products = data.products;
            $scope.vouchers = data.vouchers;
            $scope.totals = data.totals;

            console.log(data.totals);
        }

        $scope.OrderAdd = function () {

        }


    })
;