angular.module('starter.address', [])
    .controller('AddressCtrl', function ($scope, $location, webStorage, Restangular, AppConfig) {
        var AddressObj = Restangular.all('address');
        var checkoutState = AppConfig.checkout();
        var addresses = {};

        $scope.billing_address = webStorage.session.get('billing_address_id');
        $scope.shipping_address = webStorage.session.get('shipping_address_id');

        AddressObj.post().then(function (data) {
            $scope.addresses = data.addresses;
            webStorage.session.add('addresses', data.addresses);
        })

        $scope.setBillingAddress = function (addressId) {
            webStorage.session.add('billing_address_id', addressId);
            addresses = addressId;
        }

        $scope.setShippingAddress = function (addressId) {
            webStorage.session.add('shipping_address_id', addressId);
            addresses = addressId;
        }

        $scope.Next = function (state) {

            $location.path(checkoutState[state]);

        }

    });
