angular.module('starter.account', [])

    .controller("AccountCtrl", function ($scope, $cookieStore, $ionicPopup, $timeout, $location, AppConfig, Restangular, webStorage) {

        var isLog = false;
        var AccountObj = Restangular.all('oauth');


        if (webStorage.local.get('token') != null) {
            isLog = true;
        };

        $scope.token = (webStorage.local.get('token') != null) ? webStorage.local.get('token') : null;
        $scope.Show = function (status) {
            isLog = status;
        };

        $scope.IsLogged = function (status) {
            return isLog == status;
        };

        ClearAll = function () {
            webStorage.local.clear();
        }

        Authenticate = function (token) {
            console.log('Authentication: ');
            console.log(webStorage.local.get('token'));
        }

        $scope.Login = function (input) {
            var apps = AppConfig.apps();
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
        };

        $scope.Logout = function () {

            var confirmPopup = $ionicPopup.confirm({
                title: 'Logout',
                template: 'Are you sure you want sign-out?'
            });
            confirmPopup.then(function (res) {
                if (res) {
                    ClearAll();
                    $location.path('/tab/dash');
                } else {
                    console.log('You are not sure');
                }
            });

            $timeout(function () {
                confirmPopup.close();
            }, 3000);

        }

    })
