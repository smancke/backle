var backle = angular.module('backle', ['ngResource']);

backle.controller('CreateCtrl', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

    $('#name').focus();                

    $scope.alertHtmlMessage = undefined;;
    $scope.alertType = undefined;

    $scope.projectname = global_projectname;
    $scope.backlogname = global_backlogname;
    $scope.backlogtitle = global_backlogname;
    $scope.is_public_viewable = true;

    $scope.create = function() {
        $scope.alertHtmlMessage = '';
        $scope.alertType = '';

        var data = {
            projectname: $scope.projectname,
            backlogname: $scope.backlogname,
            backlogtitle: $scope.backlogtitle,
            is_public_viewable: $scope.is_public_viewable
        };

        $http.post(global_basepath +'/api/backlog', data)
            .success(function() {
                $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Backlog '"+ $scope.backlogname + "' created!</h3>Redirecting ...");
                $scope.alertType = 'alert alert-success';
                window.setTimeout(function() {
                    window.location.href = global_basepath +'/' + $scope.backlogname;
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