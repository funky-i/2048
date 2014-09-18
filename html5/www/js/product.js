angular.module('starter.product', [])

.controller("ProductCtrl", function($scope) {
    var products = [{
          product_id : 1,
          name : 'Product',
          price : 200,
          quantity : 10
        },
        {
            product_id : 2,
            name : 'Product',
            price : 200,
            quantity : 10
        }]

        this.list = function() {
            return products;
        }

    $scope.products = this.list();

});