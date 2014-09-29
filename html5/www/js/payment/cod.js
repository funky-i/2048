angular.module('cod', [])
    .controller('CODCtrl', function ($scope, $http, $location, Restangular, webStorage, AppConfig) {
        var PaymentObj = Restangular.all('payment/callback');
        var inputData = {
            cmd: '_cart'
        }
        $scope.Confirm = function () {
//            PaymentObj.post().then(function (data) {
//                console.log(data);
//            })
        }
    });
