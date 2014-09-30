angular.module('cod', [])
    .controller('CODCtrl', function ($scope, $http, $location, Restangular, webStorage, AppConfig) {
        var PaymentObj = Restangular.all('payment/callback');

        var inputData = {
            order_id: webStorage.session.get('order_id')
        }

        $scope.Confirm = function () {
            PaymentObj.post(inputData).then(function (data) {
                console.log(data);
            })
        }
    });
