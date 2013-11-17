var backle = angular.module('backle', ['ngResource']);

backle.controller('IndexCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.projects = [];

    $http.get(global_basepath +'/api/project').success(function (result) {
        $scope.projects = result;
    });

}]);



