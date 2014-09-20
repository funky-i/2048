angular.module('starter.account', [])

.controller("AccountCtrl", function($scope, Restangular, $cookieStore, $location) {
        var apps = {
            client_id : '4',
            secret : 'abc',
            grant_type : 'password'
        };

        var inputData = {};

        if ($cookieStore!==null) {
            console.log("token: " + $cookieStore.get('token').access_token);
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
                $location.path('/tab/profile');
                console.log( data);
                $cookieStore.put('token', data);
            }

        }
    }

})
