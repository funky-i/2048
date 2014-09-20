angular.module('starter.services', [])

/**
 * A simple example service that returns some data.
 */
//    .config(function ( $httpProvider) {
//        $httpProvider.defaults.useXDomain = true;
//        delete $httpProvider.defaults.headers.common['X-Requested-With'];
//    })
    .factory('AppConfig', function() {
        var apps = {
            client_id : '4',
            secret : 'abc',
            grant_type : 'password'
        };

        return apps;
    })
    .factory('Products', function($http) {
        var products = {};
        var status = false;
        var url = 'http://192.168.1.34/Projects/Opencart/Present/2.0/index.php?route=api/product';

        return {
            all: function () {

                products = $http.get(url).then(function(response){
                    return response.data;
                });

//                $http({method: 'GET', url: url})
//                .success(function(data, status, headers, config) {
////                        console.log(data);
////                    $scope.products = data;
//                })
//                .error(function(data, status, headers, config) {
//                    return false;
//                });

                return products;
            }

        }
    })

.factory('Friends', function() {
  // Might use a resource here that returns a JSON array

  // Some fake testing data
  var friends = [
    { id: 0, name: 'Scruff McGruff' },
    { id: 1, name: 'G.I. Joe' },
    { id: 2, name: 'Miss Frizzle' },
    { id: 3, name: 'Ash Ketchum' }
  ];

  return {
    all: function() {
      return friends;
    },
    get: function(friendId) {
      // Simple index lookup
      return friends[friendId];
    }
  }
});
