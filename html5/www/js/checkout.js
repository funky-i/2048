angular.module('starter.checkout', [])
    .controller('CheckoutCtrl', function ($scope, $http, $location, Restangular, webStorage, AppConfig) {
        var checkoutState = AppConfig.checkout();

        $scope.Next = function (state) {
            $location.path(checkoutState[state]);
        }
    });
