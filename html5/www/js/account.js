angular.module('starter.account', [])

.controller("Account", function($scope) {
        var customers = {
            firstname : 'Chatchaii',
            lastname : 'Kaewdok'
        }

    $scope.customers = customers;
});