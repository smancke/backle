var backle = angular.module('backle', ['ngResource']);

backle.directive('ckedit', function ($parse) {
    CKEDITOR.disableAutoInline = true;
    var counter = 0,
    prefix = '__ckd_';
    var resetdata;

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
                    editor.addCommand('blur', {
                        modes: { wysiwyg: 1, source: 1 },
                        exec: function (editor) {
                            $(':focus').blur();
                        }
                    });
                    
                    editor.ui.addButton('saveAngular', {
                        label: 'Angular Save Plugin',
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
                        label: 'cancelAngular Plugin',
                        command: 'cancel',
                        icon: global_basepath + '/app/images/cancel.png'
                    });
                }
            });

            var options = {};
            options.on = {
                key: function(e){
                    if(e.data.keyCode==27){ // ESC Key Pressed
                        e.editor.setData(resetdata);                            
                        $(':focus').blur();
                    }
                },

                blur: function (e) {
                    if (e.editor.checkDirty()) {
                        var ckValue = e.editor.getData();
                        scope.$apply(function () {
                            setter(scope, ckValue);
                        });
                        ckValue = null;
                        e.editor.resetDirty();
                    }
                }
            };
            options.removePlugins = 'sourcearea,about';
            options.extraPlugins = 'newplugin,cancelAngular';
            var editorangular = CKEDITOR.inline(element[0], options); //invoke
 
            scope.$watch(attrs.ckedit, function (value) {
                editorangular.setData(value);
                resetdata = value;
            });
        }
    }
 
});

backle.factory('Story', function($resource){
    return $resource(global_basepath +'/api/backlog/' + global_backlogname + '/' + global_storyid , {}, {
        query: {method:'GET', params:{}, isArray:false},
        update: {method:'PUT', params:{}, isArray:false}
    });
});

backle.controller('DetailCtrl', ['$scope', '$http', '$sce', '$filter', function($scope, $http, $sce, $filter) {
    $scope.permissions = global_backlog_permissions;

    $scope.storyUri = global_basepath +'/api/backlog/' + global_backlogname + '/' + global_storyid;

    $scope.alertHtmlMessage = undefined;
    $scope.alertType = undefined;

    $scope.story = {};

    $http.get($scope.storyUri).success(function(result) {
        $scope.story = result;
    });
    
//    Story.query()
//        .$promise.then(function(result) {
//            $scope.story = result;
//        },function(resultObjet) {
//            console.log('error', resultObjet);

//            $scope.alertHtmlMessage = $sce.trustAsHtml("<h3>Backlog '"+ $scope.backlogname + "' does not exist!</h3>Would you <strong><a href=\"/backle/app/create.php?backlogname="+ $scope.backlogname + "\">create "+ $scope.backlogname + "</a></strong>, now?");
//            $scope.alertType = 'alert alert-danger';
//            $scope.backlogPresent = false;
//        });

    
    $scope.$watchCollection('story', function(newValue, oldValue) {
        $http.put($scope.storyUri, newValue);
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


