angular.module('starter.checkout', [])
    .controller('CheckoutCtrl', function ($scope, $http, $location, $ionicModal, Restangular, webStorage, DeliveryMethods, AppConfig, Addresses, PaymentMethods, CartSession) {
        var OrderObj = Restangular.all('order');
        var checkoutState = AppConfig.checkout();
        var inputData = {};

        Restangular.all('cart/products').post().then(function (data) {
            if (data.products != null) {
                OrderCallback(data);
            }
        });

        OrderCallback = function (data) {
            console.log(data);

            $scope.products = data.products;
            $scope.vouchers = data.vouchers;
            $scope.totals = data.totals;
        };

        var inputData = {
            payment_address: Addresses.get(webStorage.session.get('billing_address_id')),
            payment_method: PaymentMethods.get(webStorage.session.get('payment_method_code')),
            shipping_address: Addresses.get(webStorage.session.get('shipping_address_id')),
            shipping_method: DeliveryMethods.get(webStorage.session.get('shipping_method_code'))
        };

        $scope.OpenURL = function () {
//            var ref = window.open('http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=api/payment/loaded', '_blank', 'toolbarposition=top');
            var ref = window.open('templates/payment/pp_standard.html', '_blank', 'location=yes,toolbarposition=top');

            ref.addEventListener('loadstart', function (event) {
                alert("Start:: " + event.url);
                if (event.url == 'http://localhost:8100/#/tab/cart') {
                    ref.close();
                }
            });
            ref.addEventListener('loadstop', function (event) {
                alert("Stop:: " + event.url);
            });
            ref.addEventListener('loaderror', function (event) {
                alert('Error::' + event.url);
                console.log(event);

                if (event.url == 'http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=payment/pp_standard/callback') {
                    alert('LoadError:: Complete');
                }
                ref.close();
            });
            ref.addEventListener('exit', function (event) {
                alert('Exit::' + event.url);
            })
        }

        $scope.CreateOrder = function () {

            Restangular.all('order/add').post(inputData).then(function (data) {

                if (data.success != null) {
                    $scope.CreateOrderCallback(data);
                }
            })
        };

        $scope.CreateOrderCallback = function (data) {

            console.log(data);
//            var ref = window.open('templates/payment/' + inputData.payment_method.code + '.html', '_blank', 'location=yes,toolbarposition=top');
            var code = webStorage.session.get('payment_method_code');
            var ref = window.open('http://localhost:8100/#/payment/paypal/' + data.order_id + '/' + code , '_blank', 'location=yes,toolbarposition=top');

            ref.addEventListener('loadstart', function (event) {
                alert("Start:: " + event.url);

            });
            ref.addEventListener('loadstop', function (event) {
                alert("Stop:: " + event.url);
                if (event.url == 'http://localhost/#/tab/cart/checkout') {
                    ref.close();
                }
                if (event.url == 'http://localhost/#/tab/cart/complete') {
                    alert('LoadError:: Complete');
                }
            });
            ref.addEventListener('loaderror', function (event) {
                alert('Error::' + event.url);
                console.log(event);
                ref.close();
            });
            ref.addEventListener('exit', function (event) {
                alert('Exit::' + event.url);
            })
        };

        $scope.Clear = function () {
            CartSession.clear();
        };

        $scope.Previous = function (state) {
            $location.path(checkoutState[state]);
        };
    })
;
