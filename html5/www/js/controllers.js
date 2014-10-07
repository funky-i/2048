angular.module('starter.controllers', [])

    .controller('DashCtrl', function ($scope, webStorage, Restangular, CartSession) {

        if (webStorage.local.get('customer') != null) {
            var customerData = {
                customer: webStorage.local.get('customer')
            };

            Restangular.all('customer').post(customerData).then(function (data) {
//                console.log('DashCtrl');
                if (data.error == null) {
//                    console.log(data.customer);
//                    webStorage.local.add('customer', data.customer);
                } else {
                    CartSession.clear();
                    console.log(data.error);
                }

            });
        }

    })