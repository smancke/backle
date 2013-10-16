var backle = angular.module('backle', ['ngResource']);

backle.directive('contenteditable', function() {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            // view -> model
            elm.on('blur', function() {
                scope.$apply(function() {
                    ctrl.$setViewValue(elm.html());
                });
            });

            // model -> view
            ctrl.$render = function() {
                elm.html(ctrl.$viewValue);
            };
            
            elm.on('keydown', function(event) {
                if (event.keyCode == 13 && ! event.ctrlKey) { // Enter
                    event.target.blur();
                    return false;
                }
                else if (event.keyCode == 27) { // Esc
                    elm.html(ctrl.$viewValue);
                    event.target.blur();
                }
            });
            
            elm.on('click', function(event) {
                this.focus();
            });
        }
    }
});


backle.factory('Backlog', function($resource){
    return $resource('/backle/api/backlog/:backlog/:item', {backlog: global_backlogname, item: '@id'}, {
        query: {method:'GET', params:{}, isArray:true},
        update: {method:'PUT', params:{}, isArray:false}
    });
});


backle.controller('CreateCtrl', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

    $scope.backlogname = global_backlogname;
    $scope.backlogPresent = false;
    
    if ($scope.backlogname) {
        if (global_backlogname) {
            $scope.backlogPresent = true;
        }
//        $scope.backlog = $http.get('/backle/api/backlog/'+$scope.backlogname)
//            .success(function() {
//                $scope.backlogPresent = true;
//            });
    }
}]);
