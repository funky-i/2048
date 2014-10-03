angular.module('starter.account', [])

    .controller("AccountCtrl", function ($scope, $cookieStore, $ionicPopup, $timeout, $location, AppConfig, Restangular, webStorage) {

        var isLog = false;
        var AccountObj = Restangular.all('login');

        $scope.input = {
            username: 'mr.kaewdok@gmail.com',
            password: 'demo'
        };

        if (webStorage.local.get('customer') != null) {
            isLog = true;
        };

//        $scope.token = (webStorage.local.get('token') != null) ? webStorage.local.get('token') : null;
        $scope.customer = (webStorage.local.get('customer') != null) ? webStorage.local.get('customer') : null;
        $scope.Show = function (status) {
            isLog = status;
        };

        $scope.IsLogged = function (status) {
            return isLog == status;
        };

        ClearAll = function () {
            webStorage.local.clear();
        };

        Authenticate = function (token) {
            console.log('Authentication: ');
            console.log(webStorage.local.get('customer'));
        };

        $scope.Login = function (input) {
            var inputData = {
                username: 'mr.kaewdok@gmail.com',//input.username,
                password: 'demo',//input.password,
                token: 'ef9a22c416d5bd5f875407c156db31e59d342acd'
            }

            AccountObj.post(inputData).then(function (data) {
                SuccessCallback(data);
            });

            SuccessCallback = function (data) {
                console.log(data.success);
                if (data.error != null) {
                    console.log("Err: " + data.error_description);
                } else {
                    var CustomerObj = Restangular.all('customer');
                    CustomerObj.post().then(function (data) {
                        webStorage.local.add('customer', data);
                        isLog = true;
                    });


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
