angular.module('starter.account', [])

.controller("AccountCtrl", function($scope, Restangular, $cookieStore, $ionicPopup, $timeout, $location, AppConfig) {


        var inputData = {};

        var isLog = false;

        isLog = ($cookieStore.get('token')!=null)? true : false;
        $scope.token = ($cookieStore.get('token')!=null)? $cookieStore.get('token') : '';
        $scope.Show = function(status) {
            isLog = status;
        }

        $scope.IsLogged = function(status) {
            return isLog == status;
        };

    $scope.Login = function (input) {

        var filter = {
            username : input.username,
            password : input.password,
            client_id : AppConfig.client_id,
            client_secret : AppConfig.secret,
            grant_type : AppConfig.grant_type
        }

        input = {};
        console.log(filter);

        var AccountObj = Restangular.all('api/oauth');

        AccountObj.post(filter).then(function (data) {
            SuccessCallback(data);
        });

        SuccessCallback = function(data) {

            if (data.error!=null) {
                console.log("Err: " + data.error_description);
            } else {
                isLog = true;
                console.log(data);
                $cookieStore.put('token', data);
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
