CKEDITOR.timestamp=null;

var backle = angular.module('backle', ['ngResource']);

backle.directive('ckedit', function ($parse) {
    CKEDITOR.disableAutoInline = true;
    var counter = 0,
    prefix = '__ckd_';
    var resetdata;
    var EDITOR;

    return {
        restrict: 'A',
        link: function (scope, element, attrs, controller) {
            var getter = $parse(attrs.ckedit),
                setter = getter.assign;
 
            attrs.$set('contenteditable', true); // inline ckeditor needs this
            if (!attrs.id) {
                attrs.$set('id', prefix + (++counter));
            }
 
            CKEDITOR.plugins.add('newplugin', {
                init: function (editor) {
                    EDITOR = editor;
                    editor.addCommand('blur', {
                        modes: { wysiwyg: 1, source: 1 },
                        exec: function (editor) {
                            $(':focus').blur();
                        }
                    });
                    
                    editor.ui.addButton('saveAngular', {
                        label: 'Eingabe \u00FCbernehmen',
                        command: 'blur',
                        icon: global_basepath + '/app/images/save.png'
                    });
                }
            });


            CKEDITOR.plugins.add('cancelAngular', {
                init: function (editor) {
                    editor.addCommand('cancel', {
                        modes: { wysiwyg: 1, source: 1 },
                        exec: function (editor) {
                            editor.setData(resetdata);
                            $(':focus').blur();
                        }
                    });
                    editor.ui.addButton('CancelAngular', {
                        label: 'Eingabe abbrechen',
                        command: 'cancel',
                        icon: global_basepath + '/app/images/cancel.png'
                    });
                }
            });

            // the native blur-event seems to be faster, with the one from the editor, 
            // we loose updates in case of clicking on a link
            element.on('blur', function() {
                if (EDITOR.checkDirty()) {
                    var ckValue = EDITOR.getData();
                    scope.$apply(function () {
                        setter(scope, ckValue);
                    });
                    //setter(scope, ckValue);
                    EDITOR.resetDirty();
                }
            });

            var options = {};
            options.on = {
                key: function(e){
                    if(e.data.keyCode==27){ // ESC Key Pressed
                        e.editor.setData(resetdata);                            
                        $(':focus').blur();
                    }
                }
            };

            options.removePlugins = 'sourcearea,about';
            options.extraPlugins = 'newplugin,cancelAngular';
            options.title = false;
            var editorangular = CKEDITOR.inline(element[0], options); //invoke
 
            scope.$watch(attrs.ckedit, function (value) {
                editorangular.setData(value);
                resetdata = value;
            });
        }
    }
 
});

backle.factory('Story', function($resource){
    return $resource(global_backlog_basepath + '/' + global_storyid , {}, {
        query: {method:'GET', params:{}, isArray:false},
        update: {method:'PUT', params:{}, isArray:false}
    });
});

backle.controller('DetailCtrl', ['$scope', '$http', '$sce', '$filter', function($scope, $http, $sce, $filter) {
    $scope.permissions = global_backlog_permissions;
    $scope.backlogname = global_backlogname;

    $scope.storyUri = global_backlog_basepath + '/' + global_storyid;

    $scope.alertHtmlMessage = undefined;
    $scope.alertType = undefined;

    $scope.story;

    $http.get($scope.storyUri).success(function(result) {
        $scope.story = result;

        $scope.$watch('story', function(newValue, oldValue) {
            if (newValue !== oldValue) {
                $http.put($scope.storyUri, newValue);
            }
        }, true);        
    });


    $scope.points = [
        {value: '', text: 'Not set'},
        {value: 1, text: '1'},
        {value: 3, text: '3'},
        {value: 5, text: '5'},
        {value: 7, text: '7'},
        {value: 13, text: '13'},
        {value: 20, text: '20'},
        {value: 40, text: '40'},
        {value: 80, text: '80'},
        {value: 100, text: '100'}
    ];

    $scope.showPoints = function() {
        if ($scope.story.points != undefined) {
            return $scope.story.points;
        } else {
            return 'Not set';
        }
    };
    
}]);


