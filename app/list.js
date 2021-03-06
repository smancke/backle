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
            $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Backlog not found!</h3>Either the backlog does not exist or you don't have adequate permissions.");
            $scope.alertType = 'alert alert-danger';
            $scope.backlogPresent = false;
        });

    $scope.moveStoryBefore = function(movingId, previousItemId) {
        data = {previousItem: previousItemId}
        $http.put(global_backlog_basepath +'/'+movingId+'/moveItemBehind', data);

        var from = $scope.getStoryPosition($scope.backlogItems, movingId);
        var removedItem = $scope.backlogItems.splice(from, 1)[0];
        var to = 1 + $scope.getStoryPosition($scope.backlogItems, previousItemId);
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
    $scope.getStoryPosition = function(items, storyId) {
        for (var i=0; i<items.length; i++) {
            if (items[i].id == storyId) {
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
                $http.put(global_backlog_basepath +'/'+newItem.id+'/moveItemBehind', postData);
                toPosition = $scope.getStoryPosition($scope.backlogItems, placeBehindId) + 1;
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

            // the points badge was clicked,
            // so we select the text within this
            if (span.hasClass("badge")) {
                span = span.find('span').first();
                span.focus();
                span.selectText();
            } 
            // the badge contenteditable clicked,
            // so we select the text within this
            else if (span.hasClass("badge-text")) {
                span.selectText();
            } else {
                // the main element,
                // so we select the title child element
                span = span.find(".backlog-item-title")[0];
                span.focus();
            }
        },1);
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
        var previousElement;
        var savedNodes;
        $( "#item-list" ).sortable({ 
            helper: 'clone',
            axis: 'y',  
            cursor: "move",
            cancel: '.backlog-item-title', //enable direct clicking in the text
            update: function(event, ui) {
                var movingId = $scope.getStoryIdByElement(ui.item);
                var previousId = 'begin';
                if (ui.item.prev().attr('id')) {
                    previousId = $scope.getStoryIdByElement(ui.item.prev());
                } 
                $(this).sortable('cancel');

                savedNodes.detach();
                // Put the nodes back exactly the way they started (this is very
                // important because ng-repeat uses comment elements to delineate
                // the start and stop of repeat sections and sortable doesn't
                // respect their order (even if we cancel, the order of the
                // comments are still messed up).
                if ($(this).sortable('option','helper') === 'clone') {
                    // restore all the savedNodes except .ui-sortable-helper element
                    // (which is placed last). That way it will be garbage collected.
                    savedNodes = savedNodes.not(savedNodes.last());
                }
                //$(this).empty();
                savedNodes.appendTo($(this));
                $(this).find(".ui-sortable-placeholder")[0].remove();

                $scope.$apply(function() {
                    $scope.moveStoryBefore(movingId, previousId);
                });
            },
            activate: function(event, ui) {
                previousElement = ui.item.prev().attr('id');

                // We need to make a copy of the current element's contents so
                // we can restore it after sortable has messed it up.
                // This is inside activate (instead of start) in order to save
                // both lists when dragging between connected lists.
                var focused = $(this).find(':focus')[0];
                if (focused != undefined)
                    focused.blur();
                savedNodes = $(this).contents();
            },
            deactivate: function(event, ui) {
                if (previousElement == ui.item.prev().attr('id')) {
                    var focusChild = ui.item.find(".backlog-item-title")[0];

                    focusChild.focus();
                }
            },
        });
    }

}]);
