angular.module('starter.cart', [])

    .controller("CartCtrl", function ($scope, $http, Restangular, webStorage) {
        var CartObj = Restangular.all('cart');

        $scope.CartAdd = function (productId) {
            var inputData = {
                'product_id': productId
            };

            CartObj.all('add').post(inputData).then(function (data) {
                if (data.success != null) {
                    console.log(data.success);
                    CartCallback(data);
                }
            })

            CartCallback = function (data) {
//                console.log(data);
            }

        }

        $scope.Clear = function () {

            CartObj.all('clear').post().then(function (data) {
                $scope.products = {};
                console.log("Cleared");
            })
        }


    });