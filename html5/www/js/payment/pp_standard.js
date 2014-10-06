angular.module('paypal', [])

    .controller('PaypalCtrl', function ($scope, $stateParams, $http, $location, $ionicLoading, $sce, $timeout, Restangular, webStorage, AppConfig, PaymentMethods) {

        var OrderObj = Restangular.all('payment/load');
        var OrderId = $stateParams.OrderId;
        var PaymentCode = $stateParams.PaymentCode;
        var url = {};

        var inputData = {
            order_id: OrderId
        }

        $scope.title = 'Paypal:';

        var paypalData = {
            cmd: '_cart',
            upload: 1,
            business: '',
            currency_code: 'USD',
            first_name: '',
            last_name: '',
            address1: '',
            address2: '',
            city: '',
            zip: '',
            country: '',
            address_override: 0,
            email: '',
            invoice: '',
            lc: 'en',
            rm: 2,
            no_note: 1,
            no_shipping: 1,
            charset: 'utf-8',
            return: AppConfig.payment_callback().return,
            notify_url: 'payment/pp_standard/callback',
            cancel_return: AppConfig.payment_callback().cancel_return,
            paymentaction: 'authorization',
            custom: '',
            bn: 'OpenCart_2.0_WPS'
        }

        $ionicLoading.show();

        OrderObj.post(inputData).then(function (data) {

            var data = data.payment;
            var products = [];

            angular.forEach(data.products, function (value, key) {

                var item = {
                    item_name: data.products[key].name,
                    item_number: (data.products[key].model != '') ? data.products[key].model : '',
                    amount: data.products[key].price,
                    quantity: data.products[key].quantity,
                    weight: data.products[key].weight
                }

                products.push(item);

            });

            paypalData.business = data.business;
            paypalData.currency_code = data.currency_code;
            paypalData.first_name = data.first_name;
            paypalData.last_name = data.last_name;
            paypalData.address1 = data.address1;
            paypalData.address2 = data.address2;
            paypalData.city = data.city;
            paypalData.zip = data.zip;
            paypalData.country = data.country;
            paypalData.email = data.email;
            paypalData.invoice = data.invoice;
            paypalData.lc = data.lc;
            paypalData.notify_url = data.notify_url;
            paypalData.paymentaction = data.paymentaction;
            paypalData.custom = data.custom;

            url = data.action;
            var paypal_html = '';

            paypal_html = '<form id="paypalForm" action="' + url + '" method="post">';

            angular.forEach(paypalData, function (value, key) {
                paypal_html += '<input type="hidden" name="' + key + '" value="' + value + '"/>';
            });

            angular.forEach(products, function (index, list) {
                angular.forEach(products[list], function (value, key) {
                    if (key != 'number') {
                        paypal_html += '<input type="hidden" name="' + key + '_' + (list + 1) + '" value="' + value + '" >';
                    }
                })
            });

            paypal_html += '</form>';

            $scope.formActionUrl = $sce.trustAsResourceUrl(data.action);
            $scope.paypalData = paypalData;
            $scope.products = products;
            $scope.html = $sce.trustAsHtml(paypal_html);

            $timeout(function() {
                var element = document.getElementById("paypalForm");
                element.action = url;
                element.submit();
            }, 5000);

        });

        $scope.Submit = function (data) {

            var element = document.getElementById("paypalForm");
            if (element != null) {
                return true;
            }

        }
    });
