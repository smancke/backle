var backle = angular.module('backle', ['ngResource']);

backle.controller('CreateCtrl', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

    $('#name').focus();                

    $scope.alertHtmlMessage = undefined;;
    $scope.alertType = undefined;

    $scope.projectname = global_projectname;
    $scope.name = global_backlogname;
    $scope.title = global_backlogname;
    $scope.is_public_viewable = true;

    $scope.create = function() {
        $scope.alertHtmlMessage = '';
        $scope.alertType = '';

        var uri;
        var data;

        // if no projectname was given, we are creating a project with default backlog
        if (! $scope.projectname) {

            uri = global_basepath +'/api/project';
            data = {
                name: $scope.name,
                title: $scope.title,
                is_public_viewable: $scope.is_public_viewable
            };
        }
        // but if the projectname was set,
        // we will yust create an additional backlog within the project
        else {

            uri = global_basepath +'/api/project/' + $scope.projectname +'/backlog';
            data = {
                backlogname: $scope.name,
                backlogtitle: $scope.title,
                is_public_viewable: $scope.is_public_viewable,
            };

        }

        $http.post(uri, data)
            .success(function() {
                $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>"+ $scope.title + " created!</h3>Redirecting ...");
                $scope.alertType = 'alert alert-success';
                window.setTimeout(function() {
                    window.location.href = global_basepath +'/' + ($scope.projectname ? $scope.projectname+'/' : '') + $scope.name;
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