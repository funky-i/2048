angular.module('starter.controllers', [])

.controller('DashCtrl', function($scope, webStorage, Restangular) {

        if (webStorage.local.get('customer') != null) {
            var customerData = {
                customer: webStorage.local.get('customer')
            };

            Restangular.all('customer').post(customerData).then(function (data) {
                webStorage.local.add('customer', data);
            });
        }

})

.controller('AccountCtrl', function($scope) {
});