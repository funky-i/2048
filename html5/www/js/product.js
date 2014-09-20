angular.module('starter.product', [])

.controller("ProductCtrl", function($scope, $http, $stateParams, Products, Restangular, $cookieStore) {


//        $scope.products = Products.all().then(function(result) {
//           $scope.products = result;
//        });
//        Products.all().then(function(result) {
//           $scope.products = result;
//        });

        $scope.products = Restangular.all("api/product").getList().$object;
});