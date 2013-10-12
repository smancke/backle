var backlogList = angular.module('backlogList', ['ngResource']);

backlogList.directive('contenteditable', function() {
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

backlogList.factory('Backlog', function($resource){
    return $resource('/backle/api/backlog/:backlog/:item', {backlog: 'scrumbacklog', item: '@id'}, {
        query: {method:'GET', params:{}, isArray:true},
        update: {method:'PUT', params:{}, isArray:false}
    });
});

backlogList.controller('ListCtrl', ['$scope', 'Backlog', '$http', function($scope, Backlog, $http) {
    $scope.moveStoryBefore = function(movingId, nextStoryId) {
        data = {nextStory: nextStoryId}
        $http.put('/backle/api/backlog/scrumbacklog/'+movingId+'/moveStoryBefore', data);
    }

    $scope.backlogItems = Backlog.query();

    $scope.addItem = function() {
        var newItem  = new Backlog();
        // Workarround for formating the content editable span
        newItem.title = "&nbsp;";
        newItem.$save(function(){
            $scope.backlogItems.unshift( newItem );
            window.setTimeout(function() {
                element = $('#item-title-'+newItem.id);
                element.focus();                
            }, 1);
        });        
    };

    $scope.deleteItem = function(backlogItem) {
        backlogItem.$delete();
        $scope.backlogItems = jQuery.grep($scope.backlogItems, function (value) {
            return value.id != backlogItem.id;
        });
    }

    $scope.markAsDone = function(backlogItem) {
        if (backlogItem.status != 'done') {
            backlogItem.status = 'done';
        } else {
            backlogItem.status = 'open';
        }
    }

    $scope.focus = function(event) {
        window.setTimeout(function() {
            var span = $(event.target); 
            if (!span.hasClass("backlog-item-title")) {
                span = span.find(".backlog-item-title")[0];
            }
            span.focus();
        });
    };

    $scope.itemTitleKeyPressed = function(event) {
        if (event.ctrlKey && (event.keyCode == 13 || event.keyCode == 10)) { // enter
            $scope.addItem();
            return false;
        } 
    };

    $scope.$watch('backlogItems', function(newValue, oldValue) {
        if (newValue.length != oldValue.length) {
            // add or delete .. already handeled above
            return;
        }
        for (var i = 0; i < newValue.length; i++) {
            if (!angular.equals(newValue[i], oldValue[i])) {
                if (newValue[i].title == '') {
                    // Workarround for formating the content editable span
                    newValue[i].title = "&nbsp;";
                    return; // return, because the change fires watch again
                }
                newValue[i].$update();
            }
        }
    }, true)
}]);

function connectSortables() {
    $( "#item-list" ).sortable({ 
        helper: 'clone',
        axis: 'y',  
        cursor: "move",
        stop: function(event, ui) {
                movingId = ui.item.attr('id').substring(5);
                if (ui.item.next().attr('id')) {
                    nextId = ui.item.next().attr('id').substring(5);
                } else {
                    nextId = 'end';
                }
                
                var $scope = angular.element(document.getElementsByTagName("body")[0]).scope();
                $scope.$apply(function() {
                    $scope.moveStoryBefore(movingId, nextId);
                });
        }
});
}

$(function(){connectSortables()});
