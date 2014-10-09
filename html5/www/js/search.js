angular.module('starter.search', [])

    .controller('SearchCtrl', function ($scope, $location, $http, webStorage, Restangular, CartSession) {

        function alertDismissed() {
            // do something
            alert('Notification:: alertDismissed');
        }

//        $state.go('tab.complete');
//        $location.path('/order/complete');


        $scope.Search = function() {

        }

    })
;