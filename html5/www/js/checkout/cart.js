angular.module('starter.cart', [])

    .controller("CartCtrl", function ($scope, $http, $location, $ionicPopup, Restangular, webStorage, AppConfig) {
        var CartObj = Restangular.all('cart');
        var checkoutState = AppConfig.checkout();

        $scope.CartAdd = function (productId) {
            var inputData = {
                'product_id': productId
            };

            CartObj.all('add').post(inputData).then(function (data) {
                if (data.success != null) {
                    SuccessCallback(data);
                } else {
                    console.log(data.error.option);
                }
            })

            SuccessCallback= function (data) {
//                var confirmPopup = $ionicPopup.confirm({
//                    title: 'Cart',
//                    template: 'Success: You have added product to your shopping cart!'
//                });
//                confirmPopup.then(function (res) {
//                    if (res) {
//                        ClearAll();
//                        $location.path('/tab/dash');
//                    } else {
//                        console.log('You are not sure');
//                    }
//                });
                var alertPopup = $ionicPopup.alert({
                    title: 'SUCCESS',
                    template: data.success
                });
                alertPopup.then(function(res) {
                    console.log(data.success);
                });

            }

        }

        $scope.Next = function (state) {
            $location.path(checkoutState[state]);
        }

//        $scope.Clear = function () {
//
//            CartObj.all('clear').post().then(function (data) {
//                $scope.products = {};
//                console.log("Cleared");
//            })
//        }


    });