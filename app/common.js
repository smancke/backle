
backle.factory('Backlog', function($resource){
    return $resource(global_basepath + '/api/backlog/:backlog/:item', {backlog: global_backlogname, item: '@id'}, {
        query: {method:'GET', params:{}, isArray:true},
        update: {method:'PUT', params:{}, isArray:false}
    });
});


backle.controller('HeaderCtrl', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

    $scope.backlogname = global_backlogname;
    $scope.backlogPresent = false;
    
    if ($scope.backlogname) {
        if (global_backlogname) {
            $scope.backlogPresent = true;
        }
    }
}]);
