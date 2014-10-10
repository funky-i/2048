angular.module('starter.account', [])
    .controller("LoginCtrl", function ($scope, $ionicModal, $state, $ionicPopup, $timeout, $location, AppConfig, Restangular, webStorage, ClearSession) {
        var AccountObj = Restangular.all('login');

        $scope.input = {
            username: 'mr.kaewdok@gmail.com',
            password: 'demo'
        };

        $ionicModal.fromTemplateUrl('templates/login.html', {
            scope: $scope,
            animation: 'slide-in-up'
        }).then(function(modal) {
            $scope.modal = modal;
        });
        $scope.openModal = function() {
            $scope.modal.show();
        };
        $scope.closeModal = function() {
            $scope.modal.hide();
        };
        //Cleanup the modal when we're done with it!
        $scope.$on('$destroy', function() {
            $scope.modal.remove();
        });
        // Execute action on hide modal
        $scope.$on('modal.hidden', function() {
            // Execute action
        });
        // Execute action on remove modal
        $scope.$on('modal.removed', function() {
            // Execute action
        });

        $scope.isLogged = function() {
            if (webStorage.local.get('customer')!=null) {
                $state.go('tab.account');
            } else {
                $scope.openModal();
            }
        }

        $scope.Cancellation = function() {
            $scope.closeModal();
            $state.go('tab.dash');
        }

        $scope.Registration = function() {
            $scope.closeModal();
            $state.go('tab.register');
        }

        $scope.Login = function (input) {

            var inputData = {
                username: 'mr.kaewdok@gmail.com',//input.username,
                password: 'demo',//input.password,
                client_id: AppConfig.apps().client_id,
                client_secret: AppConfig.apps().secret,
                grant_type: AppConfig.apps().grant_type
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
                        $scope.closeModal();
                        $state.go('tab.account');
                    });
                }

            }


        };


    })
    .controller("AccountCtrl", function ($scope, $ionicModal, $state, $ionicPopup, $timeout, $location, AppConfig, Restangular, webStorage, ClearSession) {

        var isLog = false;
        var AccountObj = Restangular.all('login');


//        $scope.token = (webStorage.local.get('token') != null) ? webStorage.local.get('token') : null;
        $scope.customer = (webStorage.local.get('customer') != null) ? webStorage.local.get('customer') : null;
        $scope.Show = function (status) {
            isLog = status;
        };

        $scope.IsLogged = function (status) {
            return isLog == status;
        };

        ClearAll = function () {
            ClearSession.all();
        };

        Authenticate = function (token) {
            console.log('Authentication: ');
            console.log(webStorage.local.get('customer'));
        };



        $scope.Logout = function () {

            var confirmPopup = $ionicPopup.confirm({
                title: 'Logout',
                template: 'Are you sure you want sign-out?'
            });
            confirmPopup.then(function (res) {
                if (res) {
                    Restangular.all('logout').post().then(function (data) {
                        console.log(data);
                        ClearAll();
                        $state.go('tab.dash');
                    });

                } else {
                    console.log('You are not sure');
                }
            });

            $timeout(function () {
                confirmPopup.close();
            }, 3000);

        }

    })
