angular.module('starter.checkout', [])

    .controller('CompleteCtrl', function ($scope, webStorage, Restangular, ClearSession) {
        ClearSession.cart();
    })

    .controller('CheckoutCtrl', function ($scope, $state, $http, $ionicLoading, $location, $ionicModal, Restangular, webStorage, DeliveryMethods, AppConfig, Addresses, PaymentMethods, ClearSession) {
        var OrderObj = Restangular.all('order');
        var checkoutState = AppConfig.checkout();
        var inputData = {};

        var inputData = {
            payment_address: Addresses.get(webStorage.session.get('billing_address_id')),
            payment_method: PaymentMethods.get(webStorage.session.get('payment_method_code')),
            shipping_address: Addresses.get(webStorage.session.get('shipping_address_id')),
            shipping_method: DeliveryMethods.get(webStorage.session.get('shipping_method_code'))
        };

        Restangular.all('cart/products').post(inputData).then(function (data) {
            console.log(data.totals);
            if (data.products != null) {
                OrderCallback(data);
            }
        });

        OrderCallback = function (data) {
            console.log(data.products);

            $scope.products = data.products;
            $scope.vouchers = data.vouchers;
            $scope.totals = data.totals;
        };

        $scope.CreateOrder = function () {
            Restangular.all('order/add').post(inputData).then(function (data) {
                if (data.success != null) {
                    $scope.CreateOrderCallback(data);
                }
            })
        };

        $scope.CreateOrderCallback = function (data) {

            var code = inputData.payment_method.code;
            var ref = window.open('#/payment/' + code + '/' + data.order_id + '/' + code, '_blank', 'location=no,closebuttoncaption=cancel,toolbarposition=top');

            ref.addEventListener('loadstart', function (event) {
                alert("Start:: " + event.url);
                console.log(event);
            });
            ref.addEventListener('loadstop', function (event) {
                alert('Stop::' + event.type + '-' + event.url);
            });
            ref.addEventListener('loaderror', function (event) {
                alert('Error::' + event.url);
                if (event.url == AppConfig.payment_callback().return) {
                    ref.close();
                    $state.go('tab.complete');
                }
                if (event.url == AppConfig.payment_callback().cancel_return) {
                    ref.close();
                }
            });
            ref.addEventListener('exit', function (event) {
                alert('Exit::' + event.type);
            });

        };

        $scope.Clear = function () {
            ClearSession.all();
        };

        $scope.Previous = function (state) {
            $location.path(checkoutState[state]);
        };
    })
;
