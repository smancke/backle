var backle = angular.module('backle', ['ngResource']);

// jquery-plugin for text selection
jQuery.fn.selectText = function() {
  var range, selection;
  return this.each(function() {
    if (document.body.createTextRange) {
      range = document.body.createTextRange();
      range.moveToElementText(this);
      range.select();
    } else if (window.getSelection) {
      selection = window.getSelection();
      range = document.createRange();
      range.selectNodeContents(this);
      selection.removeAllRanges();
      selection.addRange(range);
    }
  });
}

backle.controller('ListCtrl', ['$scope', 'Backlog', '$http', '$sce', function($scope, Backlog, $http, $sce) {

    $scope.permissions = global_backlog_permissions;
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
            $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Backlog '"+ $scope.backlogname + "' does not exist!</h3>Would you <strong><a href=\""+ global_basepath +"/c/create?backlogname="+ $scope.backlogname + "\">create "+ $scope.backlogname + "</a></strong>, now?");
            $scope.alertType = 'alert alert-danger';
            $scope.backlogPresent = false;
        });

    $scope.moveStoryBefore = function(movingId, previousItemId) {
        data = {previousItem: previousItemId}
        $http.put(global_basepath + '/api/backlog/' + $scope.backlogname +'/'+movingId+'/moveItemBehind', data);
        var from = $scope.getStoryPosition(movingId);
        var removedItem = $scope.backlogItems.splice(from, 1)[0];
        var to = 1 + $scope.getStoryPosition(previousItemId);
        $scope.backlogItems.splice(to, 0, removedItem);
    }

    /**
     * recalculates all milestones and saves changes
     *
     */
    $scope.recalculateMilestonePoints = function() {
        if ($scope.backlogItems == undefined)
            return;
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
                    var sprintItem = $scope.backlogItems[i];
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
        if (isMilestone) {
            newItem.type = 'milestone';
            newItem.title = 'Sprint #';
        }
        newItem.$save(function(){
            var toPosition = 0;
            if (placeBehindId) {
                postData = {previousItem: placeBehindId}
                $http.put(global_basepath +'/api/backlog/' + $scope.backlogname +'/'+newItem.id+'/moveItemBehind', postData);
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

            // the main element,
            // so we select the title child element
            if (span.hasClass("backlog-list-item")) {
                span = span.find(".backlog-item-title")[0];
                span.focus();
            } 
            // the points badge was clicked,
            // so we select the text within this
            else if (span.hasClass("badge")) {
                span = span.find('span').first();
                span.focus();
                span.selectText();
            } 
            // the badge contenteditable clicked,
            // so we select the text within this
            else if (span.hasClass("badge-text")) {
                span.selectText();
            } 
            // 
            // else ... on any other subelement, we do nothing!
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
        $scope.recalculateMilestonePoints();
        if (newValue == undefined
            || oldValue == undefined
            || newValue.length != oldValue.length) {
            // add or delete .. already handeled above
            return;
        }
        for (var i = 0; i < newValue.length; i++) {
            var oldElement = $scope.getArrayElementById(oldValue, newValue[i].id);
            if (!angular.equals(newValue[i].title, oldElement.title)
                || !angular.equals(newValue[i].text, oldElement.text)
                || !angular.equals(newValue[i].status, oldElement.status)
                || !angular.equals(newValue[i].points, oldElement.points)) {
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

    if ($scope.permissions.write) {
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
    }

}]);
