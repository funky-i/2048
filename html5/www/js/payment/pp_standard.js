angular.module('paypal', [])
    .controller('PaypalCtrl', function ($scope, $http, $location, Restangular, webStorage, AppConfig) {
        var OrderObj = Restangular.all('payment/load');
        console.log('PaypalCtrl');
        alert('PaypalCtrl');

        var paypalData = {
            pattern: {
                cmd: '_cart',
                upload: 1,
                business: 1,
                item_name_: 1,
                item_number_: 1,
                amount_: 1,
                quantity_: 1,
                weight_: 1,
                currency_code: 1,
                first_name: 1,
                last_name: 1,
                address1: 1,
                address2: 1,
                city: 1,
                zip: 1,
                country: 1,
                address_override: 1,
                email: 1,
                invoice: 1,
                lc: 1,
                rm: 2,
                no_note: 1,
                charset: 'utf-8',
                return: 1,
                notify_url: 1,
                cancel_return: 1,
                paymentaction: 1,
                custom: 1,
                bn: 'OpenCart_2.0_WPS'
            }
        }

        var inputData = {
            order_id: webStorage.session.get('order_id'),
            payment_method: PaymentMethods.get(webStorage.session.get('payment_method_code'))
        }
        console.log(inputData);
        var url = {
            paymentURL: 'https://www.paypal.com/cgi-bin/webscr',
            cancelURL: '',
            successURL: '',
            paymentURLDemo: 'http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=api/payment/load'
        };

        OrderObj.post(inputData).then(function (data) {

        })

//        var ref = window.open(url.paymentURLDemo, '_blank', 'location=yes');

        $scope.url = url;

        $scope.Confirm = function () {
            console.log("Clicked");

//            var ref = window.open(url.paymentURLDemo, '_blank', 'location=yes');

//            $http({ method: 'POST', url: url.paymentURL, data: input})
//                .success(function(data, status){
//                    console.log(data);
//                })
//                .error(function(data, status) {
//                    console.log("ERROR");
//                });

//            var ref = window.open('http://localhost/Projects/Opencart/Present/2.0/index.php?route=api/payment/load', '_blank', 'location=yes');

//            iabLoadStart = function () {
//                console.log(event);
////                alert(event.type + ' - ' + event.url);
//            };
//
//            myCallback = function () {
//                console.log('myCallBack');
//            };

//            var ref = window.open(url.paymentURLDemo, '_blank', 'location=yes');
//            ref.addEventListener('loadstart', function() {
//                ref.execScript({ file: 'templates/payment/paypal.js' });
//            });
//            ref.addEventListener('loadstart', function(event){
//                console.log('LoadStart');
////                ref.execScript({ file: 'templates/payment/paypal.js' });
//            });
//            ref.addEventListener('loadstop', iabLoadStart);
//            ref.addEventListener('loaderror', myCallback);

//            ref.addEventListener('loadstart', function(event) { alert(event.url); });
//            ref.addEventListener('loadstop', function(event) { alert(event.url); });
//            ref.addEventListener('loaderror', function(event) { alert(event.url); });

//            ref.close();
//            var myCallback = function() { alert(event.url); }
//            ref.addEventListener('loadstart', myCallback);
//            ref.removeEventListener('loadstart', myCallback);


        }
    })
