angular.module('starter.payment', [])
    .controller('PaymentCtrl', function($scope, $location, $http, Restangular, Addresses, AppConfig, webStorage) {
        var PaymentObj = Restangular.all('payment');
        var checkoutState = AppConfig.checkout();

        var billing_address_id = webStorage.session.get('billing_address_id');
        var inputData = {
            payment_address: Addresses.get(billing_address_id)
        };

        $scope.payment_method = webStorage.session.get('payment_method_code');

        PaymentObj.post(inputData).then(function(data) {
            $scope.payment_methods = data.payment_methods;
            webStorage.session.add('payment_methods', data.payment_methods);
        })

        $scope.setPaymentMethod = function (paymentCode) {
//            console.log(paymentCode);
            webStorage.session.add('payment_method_code', paymentCode);
        }

        $scope.Next = function(state) {
            $location.path(checkoutState[state]);
        }

    });
