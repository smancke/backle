backle.filter('dbDataToJs', function() {
  return function(input) {
      var t = String(input).split(/[- :]/);
      return new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
  };
});

backle.directive('numberonly', function() {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {            
            elm.on('keypress', function(event) {
                if (event.charCode == 0) // Enter, Del, Arrow-Key, etc.
                    return true;                    
                return event.charCode >= 48 && event.charCode <= 57;
            });
        }
    }
});

backle.directive('contenteditable', function() {
    // fix for correct blur on webkit based browser
    var editableFix = $('<input style="width:1px;height:1px;border:none;margin:0;padding:0;" tabIndex="-1">').appendTo('html');

    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            
            var maxlength = attrs.maxlength;

            var placeholderText = attrs.placeholder;
            if (placeholderText == undefined) {
                placeholderText = '.. edit ..';
            }

            // view -> model
            elm.on('blur', function() {
                scope.$apply(function() {
                    var text = elm.text().trim();                    
                    ctrl.$setViewValue(text);
                    if (ctrl.$viewValue == '') {
                        elm.text(placeholderText);
                        elm.addClass("placeholder");
                    }
                });
            });

            elm.on('focus', function() {
                if (elm.text() == placeholderText) {
                    elm.text('');
                    elm.removeClass("placeholder");
                }
            });
            
            // model -> view
            ctrl.$render = function() {
                elm.text(ctrl.$viewValue);
                if (ctrl.$viewValue == null || ctrl.$viewValue == '') {
                    elm.text(placeholderText);
                    elm.addClass("placeholder");
                } else {
                    elm.removeClass("placeholder");
                }
            };

            /**
             * handling the maxlength parameter
             */
            elm.on('keypress', function(event) {
                if (maxlength == undefined)
                    return true;

                if (event.charCode == 0) // Enter, Del, Arrow-Key, etc.
                    return true;                    
                return elm.text().length < maxlength;
            });
            
            elm.on('keydown', function(event) {
                if (event.keyCode == 13 && ! event.ctrlKey) { // Enter
                    event.target.blur();

                    // fix for correct blur on webkit based browser
                    editableFix[0].setSelectionRange(0, 0);
                    editableFix.blur();
                }
                else if (event.keyCode == 27) { // Esc
                    elm.text(ctrl.$viewValue);
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
    return $resource(global_backlog_basepath + '/:item', {item: '@id'}, {
        query: {method:'GET', params:{}, isArray:true},
        update: {method:'PUT', params:{}, isArray:false}
    });
});


backle.controller('HeaderCtrl', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

    $scope.backlogname = global_backlogname;
    $scope.backlogPresent = false;
    
    if ($scope.backlogname) {
        $scope.backlogPresent = true;
    }
}]);
