angular.module('starter.account', [])

.controller("AccountCtrl", function($scope, Restangular, $cookieStore, $ionicPopup, $timeout, $location) {
        var apps = {
            client_id : '4',
            secret : 'abc',
            grant_type : 'password'
        };

        var inputData = {};

        if ($cookieStore!==null) {
//            $location.path('/tab/profile');
        };

    $scope.token = ($cookieStore!==null)? $cookieStore.get('token') : null;
    $scope.apps = apps;
    $scope.Login = function (input) {

        var fillter = {
            username : 'mr.kaewdok@gmail.com',
            password : 'demo',
            client_id : apps.client_id,
            client_secret : apps.secret,
            grant_type : apps.grant_type
        }

        input = {};

        var AccountObj = Restangular.all('api/oauth');

        AccountObj.post(fillter).then(function (data) {
            SuccessCallback(data);
        });

        SuccessCallback = function(data) {

            if (data.error!=null) {
                console.log("Err: " + data.error_description);
            } else {
                $cookieStore.put('token', data);
                $location.path('/tab/account');
            }

        }
    };
       
    $scope.Logout = function() {

            var confirmPopup = $ionicPopup.confirm({
                title: 'Logout',
                template: 'Are you sure you want sign-out?'
            });
            confirmPopup.then(function(res) {
                if(res) {
                    $cookieStore.remove('token');
                    $location.path('/tab/dash');
                } else {
                    console.log('You are not sure');
                }
            });

        $timeout(function() {
            confirmPopup.close();
        }, 3000);

//        $cookieStore.remove('token');
//        $location.path('/tab/account');
    }

})
