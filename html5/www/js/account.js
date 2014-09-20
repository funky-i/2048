angular.module('starter.account', [])

.controller("Account", function($scope, Restangular, $http) {
        var apps = {
            client_id : '4',
            secret : 'abc',
            grant_type : 'password'
        };

        var inputData = {};

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
            console.log("Success");
            console.log(data);
        }

    }

});