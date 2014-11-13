angular.module('cod', [])
    .controller('CODCtrl', function ($scope, $stateParams, $http, $location, $ionicLoading, $sce, $timeout, Restangular, webStorage, AppConfig, PaymentMethods) {

        var OrderObj = Restangular.all('payment/load');
        var OrderId = $stateParams.OrderId;
        var PaymentCode = $stateParams.PaymentCode;
        var url = {};

        var inputData = {
            order_id: OrderId,
            payment_code: PaymentCode
        }

        $scope.title = 'Cash On Delivery';

        $ionicLoading.show();

        OrderObj.post(inputData).then(function (data) {
            var data = data.payment;
            var products = [];

            url = AppConfig.payment_callback().return;

            paypal_html = '<form id="paypalForm" action="' + url + '" method="post">';
            paypal_html += '<input type="hidden" name="payment_code" value="cod"/>';
            paypal_html += '<input type="hidden" name="order_id" value="' + OrderId + '"/>';
            paypal_html += '</form>';

            $scope.formActionUrl = $sce.trustAsResourceUrl(data.action);
            $scope.html = $sce.trustAsHtml(paypal_html);

            $timeout(function() {
                $scope.Confirm(inputData);
            }, 2000);
        });

        $scope.Confirm = function (data) {

            Restangular.all('payment/callback').post(inputData).then(function (data) {
                if (data.success == true) {
                    $timeout(function() {
                        var element = document.getElementById("paypalForm");
                        element.action = url;
                        element.submit();
                    }, 2000);
                } else {
                    //alert('error' + data.success);
                }
            });

        }


    });
