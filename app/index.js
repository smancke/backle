var backle = angular.module('backle', ['ngResource']);

backle.controller('IndexCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.backlogs = [];

    $http.get('/backle/api/backlog').success(function (result) {
        $scope.backlogs = result;
    });

}]);



