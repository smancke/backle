var backle = angular.module('backle', ['ngResource', 'xeditable']);

backle.run(function(editableOptions) {
  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
});

backle.directive('ckedit', function ($parse) {
    CKEDITOR.disableAutoInline = true;
    var counter = 0,
    prefix = '__ckd_';
 
    return {
        restrict: 'A',
        link: function (scope, element, attrs, controller) {
            var getter = $parse(attrs.ckedit),
                setter = getter.assign;
 
            attrs.$set('contenteditable', true); // inline ckeditor needs this
            if (!attrs.id) {
                attrs.$set('id', prefix + (++counter));
            }
 
            // CKEditor stuff
            // Override the normal CKEditor save plugin
 
            CKEDITOR.plugins.registered['save'] =
            {
                init: function (editor) {
                    editor.addCommand('save',
                        {
                            modes: { wysiwyg: 1, source: 1 },
                            exec: function (editor) {
                                if (editor.checkDirty()) {
                                    var ckValue = editor.getData();
                                    scope.$apply(function () {
                                        setter(scope, ckValue);
                                    });
                                    ckValue = null;
                                    editor.resetDirty();
                                }
                            }
                        }
                    );
                    editor.ui.addButton('Save', { label: 'Save', command: 'save', toolbar: 'document' });
                }
            };
            var options = {};
            options.on = {
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
            //options.extraPlugins = 'sourcedialog';
            options.removePlugins = 'sourcearea';
            var editorangular = CKEDITOR.inline(element[0], options); //invoke
 
            scope.$watch(attrs.ckedit, function (value) {
                editorangular.setData(value);
            });
        }
    }
 
});

backle.factory('Story', function($resource){
    return $resource('/backle/api/backlog/' + global_backlogname + '/' + global_storyid , {}, {
        query: {method:'GET', params:{}, isArray:false},
        update: {method:'PUT', params:{}, isArray:false}
    });
});

backle.controller('DetailCtrl', ['$scope', '$http', '$sce', '$filter', function($scope, $http, $sce, $filter) {

    $scope.storyUri = '/backle/api/backlog/' + global_backlogname + '/' + global_storyid;

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
        console.log('newValue: ' + JSON.stringify(newValue));
        $http.put($scope.storyUri, newValue);
        //newValue.$update();
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


