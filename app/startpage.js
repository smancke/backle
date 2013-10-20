var backle = angular.module('backle', ['ngResource']);

backle.controller('IndexCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.backlogs = [];

    $http.get(global_basepath +'/api/backlog').success(function (result) {
        $scope.backlogs = result;
    });

}]);



