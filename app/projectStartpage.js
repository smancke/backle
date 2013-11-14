var backle = angular.module('backle', ['ngResource']);

backle.controller('ProjectIndexCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.backlogs = [];

    $http.get(global_basepath +'/api/backlog?projectname='+global_projectname).success(function (result) {
        $scope.backlogs = result;
    });

}]);



