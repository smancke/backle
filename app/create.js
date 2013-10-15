var backle = angular.module('backle', ['ngResource']);

backle.controller('CreateCtrl', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

    $('#name').focus();                

    $scope.alertHtmlMessage = undefined;;
    $scope.alertType = undefined;

    $scope.backlogname = global_backlogname;

    $scope.create = function() {
        $scope.alertHtmlMessage = '';
        $scope.alertType = '';

        var data = {
            backlogname: $scope.backlogname,
            title: $scope.backlogname
        };

        $http.post('/backle/api/backlog', data)
            .success(function() {
                $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Backlog '"+ $scope.backlogname + "' created!</h3>Redirecting ...");
                $scope.alertType = 'alert alert-success';
                window.setTimeout(function() {
                    window.location.href = '/backle/app/list.php?backlogname=' + $scope.backlogname;
                },1000);
            })
            .error(function(result) {
                if (result.message) {
                    $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>"+ result.message +"</h3>");
                    $scope.alertType = 'alert alert-warning';
                    $('#name').focus();                
                } else {
                    $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Unknown error ("+result.status+")!</h3>");
                    $scope.alertType = 'alert alert-danger';
                }
            });
    }
}]);