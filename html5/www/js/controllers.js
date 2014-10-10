angular.module('starter.controllers', [])
    .controller('BodyCtrl', function($scope) {
        ionic.Platform.ready(function() {
            // hide the status bar using the StatusBar plugin
            StatusBar.hide();
        });
    })
    .controller('DashCtrl', function ($scope, webStorage, Restangular, ClearSession) {

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
                    ClearSession.all();
                    console.log(data.error);
                }

            });
        }

    });
