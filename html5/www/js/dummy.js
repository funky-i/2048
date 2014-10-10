angular.module('starter.dummy', [])
    .controller("DummyCtrl", function ($scope, $state, $ionicTabsDelegate, $ionicLoading, $location, $ionicModal, webStorage) {
//        $state.go('tab.complete');
//        $location.path('/order/complete');
        console.log('DummyCtrl');
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
            console.log('modal.hidden');
        });
        // Execute action on remove modal
        $scope.$on('modal.removed', function() {
            console.log('modal.removed');
            // Execute action
        });

        $scope.isLogged = function() {
            var isLog = false;

            if (isLog) {
                $state.go('tab.account');
            } else {
                $scope.openModal();
            }
        }

        $scope.Cancellation = function() {
            console.log('Cancellation');
            $scope.modal.hide();
            $state.go('tab.dash');
        }

        $scope.Registration = function() {
            console.log('Registrator');
            $scope.modal.hide();
            $state.go('tab.register');
        }

        $scope.$watch('$viewContentLoaded', function() {
//            $ionicSlideBoxDelegate.$getByHandle('news-slide').update();
//            //console.log(1);
        })

        function alertDismissed() {
            // do something
        }

        navigator.notification.alert(
            'You are the winner!',  // message
            alertDismissed,         // callback
            'Game Over',            // title
            'Done'                  // buttonName
        );

    })
;
