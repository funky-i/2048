angular.module('starter.product', [])

    .controller("ProductCtrl", function ($scope, $http, $stateParams, Restangular, localStorageService) {
        var ProductObj = Restangular.all("product/lists");
        var inputData = {
//            'filter_page': 1,
//            'filter_limit': 10
        }

        $scope.leftButtons = [{
            type: 'button-icon icon ion-navicon',
            tap: function(e) {
                alert('tabs');
            }
        }];

        ProductObj.post(inputData).then(function (data) {
            $scope.products = data;
        });
    })

    .controller("ProductDetailCtrl", function ($scope, $stateParams, Restangular) {
        var productId = $stateParams.productId;
        var ProductObj = Restangular.all('product');
        var productInfo = {};

        $scope.product_id = productId;

        var inputData = {
            'product_id': $stateParams.productId
        };

        ProductObj.post(inputData).then(function (data) {
            SuccessCallback(data);
        });

        SuccessCallback = function (data) {
            $scope.productInfo = data;
        }

        $scope.productDetail = function (productId) {
            console.log("ProductId: " + productId);

//            return "ProductInfo";
//            var RestBaseURL = Restangular.all("api/product");
//
//            var inputData = {
//                'product_id' : $stateParams.productId
//            }
//            var product_info = {};
////            product_info = RestBaseURL.post(inputData).then(function (data) {
////                console.log(data);
////            });
////            $scope.friend = Friends.get($stateParams.friendId);
        };
    })
;