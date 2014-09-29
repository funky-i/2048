angular.module('starter.oauth', [])
    .controller('OAuthCtrl', function($scope, Restangular, webStorage) {
        var isLog = false;
        var AccountObj = Restangular.all('oauth');

        $scope.OAuthLogin = function(input) {
            var inputData = {
                username: 'mr.kaewdok@gmail.com',//input.username,
                password: 'demo',//input.password,
                client_id: apps.client_id,
                client_secret: apps.secret,
                grant_type: apps.grant_type
            }

            AccountObj.post(inputData).then(function (data) {
                SuccessCallback(data);
                console.log(data);
            });

            SuccessCallback = function (data) {

                if (data.error != null) {
                    console.log("Err: " + data.error_description);
                } else {
                    isLog = true;
                    webStorage.local.add('token', data);
                }

            }

            input = {};
        }
    })
;
