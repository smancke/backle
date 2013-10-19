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


backle.controller('ListCtrl', ['$scope', 'Backlog', '$http', '$sce', function($scope, Backlog, $http, $sce) {

    $scope.backlogname = global_backlogname;

    $scope.backlogPresent = false;

    $scope.alertHtmlMessage = undefined;;
    $scope.alertType = undefined;;

    $scope.backlogItems;

    /**
     * the total sum of story points.
     * recalculated by recalculateMilestonePoints
     */
    $scope.totalStoryPoints;

    Backlog.query()
        .$promise.then(function(result) {
            $scope.backlogItems = result;
            $scope.backlogPresent = true;
            $scope.recalculateMilestonePoints();
        },function() {
            $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Backlog '"+ $scope.backlogname + "' does not exist!</h3>Would you <strong><a href=\"/backle/app/create.php?backlogname="+ $scope.backlogname + "\">create "+ $scope.backlogname + "</a></strong>, now?");
            $scope.alertType = 'alert alert-danger';
            $scope.backlogPresent = false;
        });

    $scope.moveStoryBefore = function(movingId, previousItemId) {
        data = {previousItem: previousItemId}
        $http.put('/backle/api/backlog/' + $scope.backlogname +'/'+movingId+'/moveItemBehind', data);
        var from = $scope.getStoryPosition(movingId);
        var removedItem = $scope.backlogItems.splice(from, 1)[0];
        var to = 1 + $scope.getStoryPosition(previousItemId);
        $scope.backlogItems.splice(to, 0, removedItem);
        $scope.recalculateMilestonePoints();
    }

    /**
     * recalculates all milestones and saves changes
     *
     */
    $scope.recalculateMilestonePoints = function() {
        var total = 0;
        var sum = 0;
        for (var i=0; i<$scope.backlogItems.length; i++) {
            if ($scope.backlogItems[i].type == 'story') {
                if ($scope.backlogItems[i].points != undefined) {
                    sum += parseInt($scope.backlogItems[i].points);
                    total += parseInt($scope.backlogItems[i].points)
                }
            } else {
                if ($scope.backlogItems[i].points != sum) {
                    $scope.backlogItems[i].points = sum;
                }
                sum = 0;
            }
        }
        $scope.totalStoryPoints = total;
    }
    
    /**
     * Return the position within the list of stories
     */
    $scope.getStoryPosition = function(storyId) {
        for (var i=0; i<$scope.backlogItems.length; i++) {
            if ($scope.backlogItems[i].id == storyId) {
                return i;
            }
        }
    }

    $scope.addSprint = function() {
        $scope.addItem(false, true);
    }

    /**
     * creates a new story.
     * 'placeBehindId': optional parameter, the story id to place the item behind
     */
    $scope.addItem = function(placeBehindId, isMilestone) {
        var newItem  = new Backlog();
        // Workarround for formating the content editable span
        newItem.title = "&nbsp;";
        if (isMilestone) {
            newItem.type = 'milestone';
            newItem.title = 'Sprint #';
        }
        newItem.$save(function(){
            var toPosition = 0;
            if (placeBehindId) {
                postData = {previousItem: placeBehindId}
                $http.put('/backle/api/backlog/' + $scope.backlogname +'/'+newItem.id+'/moveItemBehind', postData);
                toPosition = $scope.getStoryPosition(placeBehindId) + 1;
            } 
            $scope.backlogItems.splice(toPosition, 0, newItem);
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
            var editingStoryId = $scope.getStoryIdByElement(event.target);
            $scope.addItem(editingStoryId);
            return false;
        } 
    };

    /**
     * returns the ellement with the given id from the given array.
     */
    $scope.getArrayElementById = function(elementArray, id) {
        var resultList = jQuery.grep(elementArray, function (value) {
            return value.id == id;
        });
        return resultList[0];
    }

    $scope.$watch('backlogItems', function(newValue, oldValue) {
        if (newValue == undefined
            || oldValue == undefined
            || newValue.length != oldValue.length) {
            // add or delete .. already handeled above
            return;
        }
        for (var i = 0; i < newValue.length; i++) {
            var oldElement = $scope.getArrayElementById(oldValue, newValue[i].id);
            if (!angular.equals(newValue[i], oldElement)) {
                if (newValue[i].title == '') {
                    // Workarround for formating the content editable span
                    newValue[i].title = "&nbsp;";
                    return; // return, because the change fires watch again
                }
                newValue[i].$update();
            }
        }
    }, true)

    $scope.getStoryIdByElement = function(storyWidget) {
        var id = $(storyWidget).attr('id');
        id = id.replace(/item-title-/, '');
        id = id.replace(/item-/, '');
        return id;
    }

    $( "#item-list" ).sortable({ 
        helper: 'clone',
        axis: 'y',  
        cursor: "move",
        stop: function(event, ui) {
            var movingId = $scope.getStoryIdByElement(ui.item);
            var previousId = 'begin';
            if (ui.item.prev().attr('id')) {
                previousId = $scope.getStoryIdByElement(ui.item.prev());
            } 
            $scope.$apply(function() {
                $scope.moveStoryBefore(movingId, previousId);
            });
        }
    });

}]);
