var index = angular.module('index', []);

index.controller('IndexCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.backlogs = [];

    $http.get('/backle/api/backlog').success(function (result) {
        $scope.backlogs = result;
    });

}]);



