angular.module('paysbuy', [])

    .controller('PaySbuyCtrl', function ($scope, $stateParams, $http, $location, $ionicLoading, $sce, $timeout, Restangular, webStorage, AppConfig, PaymentMethods) {

        var OrderObj = Restangular.all('payment/load');
        var OrderId = $stateParams.OrderId;
        var PaymentCode = $stateParams.PaymentCode;
        var url = {};

        var inputData = {
            order_id: OrderId
        }

        $scope.title = 'Connection to PaySbuy';

        var paysbuyData = {
            psb: '1',
            biz: '',
            securecode: '',
            currencyCode: '',
            inv: '',
            postURL: AppConfig.payment_callback().return,
            reqURL: 'payment/paysbuy/callback',
            itm: 0,
            amt: 0
        }

        $ionicLoading.show();

        OrderObj.post(inputData).then(function (data) {
            console.log('PaySbuyCtrl');
            console.log(data.payment);
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

            paysbuyData.psb = data.psbid;
            paysbuyData.biz = data.username;
            paysbuyData.securecode = data.securecode;
            paysbuyData.currencyCode = data.currencyCode;
            paysbuyData.inv = data.invoice;

            paysbuyData.itm = data.item_name;
            paysbuyData.amt = data.total;
            paysbuyData.reqURL = data.notify_url;
            paysbuyData.custom = data.custom;

            url = data.action;
            var paysbuy_html = '';

            paysbuy_html = '<form id="paysbuyForm" action="' + url + '" method="post">';

            angular.forEach(paysbuyData, function (value, key) {
                paysbuy_html += '<input type="hidden" name="' + key + '" value="' + value + '"/>';
            });

            paysbuy_html += '</form>';

            $scope.formActionUrl = $sce.trustAsResourceUrl(data.action);
            $scope.paypalData = paysbuyData;
            $scope.products = products;
            $scope.html = $sce.trustAsHtml(paysbuy_html);

            $timeout(function() {
                var element = document.getElementById("paysbuyForm");
                element.action = url;
                element.submit();
            }, 5000);

        });

        $scope.Submit = function (data) {

            var element = document.getElementById("paysbuyForm");
            if (element != null) {
                return true;
            }

        }
    });
