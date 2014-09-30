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

//        var inputData = {
//            payment_address: Addresses.get(webStorage.session.get('billing_address_id')),
//            payment_method: PaymentMethods.get(webStorage.session.get('payment_method_code')),
//            shipping_address: Addresses.get(webStorage.session.get('shipping_address_id')),
//            shipping_method: ShippingMethods.get(webStorage.session.get('shipping_method_code'))
//        }

        $ionicModal.fromTemplateUrl('templates/payment/pp_standard.html', {
            scope: $scope,
            animation: 'slide-in-up',
            backdropClickToClose: true
        }).then(function (modal) {
            $scope.modal = modal;
        });

        $scope.openModal = function () {
            $scope.modal.show();
        };
        $scope.closeModal = function () {
            $scope.modal.hide();
        };

        $scope.AddOrder = function () {

//            OrderObj.all('add').post(inputData).then(function (data) {
//                if (data.success != null) {
//                    webStorage.session.add('order_id', data.order_id);
//                }
//            })

            $scope.openModal();

        }

        $scope.Confirm = function () {
            $location.path(checkoutState[0]);
        }

        $scope.Previous = function (state) {
            $location.path(checkoutState[state]);
        }
    });
