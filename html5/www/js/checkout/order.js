angular.module('starter.order', [])
    .controller('OrderCtrl', function ($scope, $http, $location, $ionicPopover, Restangular, Orders, webStorage, AppConfig, DeliveryMethods) {
        var OrderObj = Restangular.all('cart/products');
        var checkoutState = AppConfig.checkout();
        var inputData = {};

//        inputData = {
//            shipping_method: {}
//        }
        $ionicPopover.fromTemplateUrl('templates/popover.html', function(popover) {
            $scope.popover = popover;
        });

        $scope.data = {
            showDelete: false
        };

        $scope.up = function(item) {
            alert('Edit Item: ' + item.product_id);
        };
        $scope.down = function(item) {
            alert('Share Item: ' + item.product_id);
        };
        $scope.del = function(item) {
            alert('Share Item: ' + item.product_id);
        };

        $scope.moveItem = function(item, fromIndex, toIndex) {
            $scope.items.splice(fromIndex, 1);
            $scope.items.splice(toIndex, 0, item);
        };

        $scope.onItemDelete = function(item) {
            $scope.items.splice($scope.items.indexOf(item), 1);
        };

        OrderObj.post().then(function (data) {
            console.log(data.totals);
            if (data.products!=null) {
                OrderCallback(data);
            }
        });

        OrderCallback = function (data) {
            console.log('OrderCtrl');
            console.log(data.products);

            $scope.products = data.products;
            $scope.vouchers = data.vouchers;
            $scope.totals = data.totals;

            webStorage.session.add('products', data.products);
            webStorage.session.add('vouchers', data.vouchers);
            webStorage.session.add('totals', data.totals);

        }

        $scope.Next = function (state) {
            $location.path(checkoutState[state]);
        }

        $scope.Clear = function () {
            Restangular.all('cart/clear').post().then(function (data) {
//                console.log(data);
                $scope.products = {};
                $scope.vouchers = {};
                $scope.totals = data.totals;
            })
        }


    })
;