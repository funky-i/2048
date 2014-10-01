angular.module('starter.checkout', [])
    .controller('CheckoutCtrl', function ($scope, $http, $location, $ionicModal, Restangular, webStorage, AppConfig, Addresses, PaymentMethods, ShippingMethods) {
        var OrderObj = Restangular.all('order');
        var checkoutState = AppConfig.checkout();

        Restangular.all('cart/products').post().then(function (data) {
            if (data.products != null) {
                OrderCallback(data);
            }
        });

        OrderCallback = function (data) {
            $scope.products = data.products;
            $scope.vouchers = data.vouchers;
            $scope.totals = data.totals;
        }

        var inputData = {
            payment_address: Addresses.get(webStorage.session.get('billing_address_id')),
            payment_method: PaymentMethods.get(webStorage.session.get('payment_method_code')),
            shipping_address: Addresses.get(webStorage.session.get('shipping_address_id')),
            shipping_method: ShippingMethods.get(webStorage.session.get('shipping_method_code'))
        }

        console.log(inputData);

//        $ionicModal.fromTemplateUrl('templates/payment/cod.html', {
//            scope: $scope,
//            animation: 'slide-in-up',
//            backdropClickToClose: true
//        }).then(function (modal) {
//            $scope.modal = modal;
//        });

//        $scope.OnPayPalSubmited = function (input) {
//            document.getElementById('paypalForm').submit();
//        }


//        $scope.OpenURL = function () {
////            var ref = window.open('http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=api/payment/loaded', '_blank', 'toolbarposition=top');
//            var ref = window.open('templates/payment/pp_standard.html', '_blank', 'location=yes,toolbarposition=top');
//
//            ref.addEventListener('loadstart', function (event) {
//                alert("Start:: " + event.url);
//                if (event.url == 'http://localhost:8100/#/tab/cart') {
//                    ref.close();
//                }
//            });
//            ref.addEventListener('loadstop', function (event) {
//                alert("Stop:: " + event.url);
//            });
//            ref.addEventListener('loaderror', function (event) {
//                alert('Error::' + event.url);
//                console.log(event);
//
//                if (event.url=='http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=payment/pp_standard/callback'){
//                    alert('LoadError:: Complete');
//                }
//                ref.close();
//            });
//            ref.addEventListener('exit', function (event) {
//                alert('Exit::' + event.url);
//            })
//        }

//        $scope.openModal = function () {
//            $scope.modal.show();
////            var ref = window.open('templates/payment/paypal.html','_self', 'location=yes');
//        };
//        $scope.closeModal = function () {
//            $scope.modal.hide();
//        };

        $scope.CreateOrder = function () {

            OrderObj.all('add').post(inputData).then(function (data) {
                if (data.success != null) {
                    $scope.CreateOrderCallback(data);
                }
            })

        }

        $scope.CreateOrderCallback = function (data) {
            console.log(data);
            webStorage.session.add('order_id', data.order_id);
            var payment_template = inputData.payment_method.code;
            var ref = window.open('templates/payment/' + payment_template + '.html', '_blank', 'location=yes,toolbarposition=top');

            ref.addEventListener('loadstart', function (event) {
                alert("Start:: " + event.url);
            });
            ref.addEventListener('loadstop', function (event) {
                alert("Stop:: " + event.url);
//                    if (event.url == 'http://localhost:8100/#/tab/cart') {
//                        ref.close();
//                    }
            });
            ref.addEventListener('loaderror', function (event) {
                alert('Error::' + event.url);
                console.log(event);

                if (event.url == 'http://localhost/#/tab/cart/complete') {
                    alert('LoadError:: Complete');
//                    $scope.Clear();
                }
                if (event.url == 'http://localhost:8100/#/tab/cart') {
                    ref.close();
                }

            });
            ref.addEventListener('exit', function (event) {
                alert('Exit::' + event.url);
            })
        }

        $scope.Clear = function () {
            webStorage.session.remove('billing_address_id');
            webStorage.session.remove('order_id');
            webStorage.session.remove('payment_method_code');
            webStorage.session.remove('products');
            webStorage.session.remove('shipping_address_id');
            webStorage.session.remove('shipping_method_code');
            webStorage.session.remove('totals');
            webStorage.session.remove('vouchers');
        }

        $scope.Previous = function (state) {
            $location.path(checkoutState[state]);
        }
    });
