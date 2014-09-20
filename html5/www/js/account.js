angular.module('starter.account', [])

.controller("Account", function($scope, Restangular, $http) {
        var apps = {
            client_id : '4',
            secret : 'abc',
            grant_type : 'password'
        };
        
        var url = 'http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=api/oauth';

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

        console.log("OAuth LOGIN");
        console.log(fillter);
        input = {};

//        Restangular.post('api/oauth', fillter).getList().$object;

        $http({method: 'POST', url: url, data: fillter})
            .success(function(data, status) {
                console.log(data);
            })
            .error(function(data, status){
                console.log("Error: " + data);
            })

//        Restangular.post('api/oauth', fillter).then(function() {
//
//            console.log("Object Save");
//        }, function() {
//            console.log("There was an error saving");
//        });

//        console.log(callback);
    }
//        this.login = function(login) {
//            console.log(login);
////            var newAccount = {name: "Gonto's account"};
////            baseAccounts.post(newAccount);
//        };
});