describe('Backlog controllers', function() {
    
    describe('ListCtrl', function(){

        var scope, Backlog, ctrl;
 
        beforeEach(module('backlogList'));

        beforeEach(inject(function(_$http_, $rootScope, $controller, $http, _Backlog_) {
            Backlog = _Backlog_

            scope = $rootScope.$new();
            ctrl = $controller('ListCtrl', {$scope: scope, Backlog: Backlog, $http: _$http_});
        }));

        it('should have loaded some items', function() {
            // TODO: User mock httpBackend
            //expect(ctrl.backlogItems.length).toBe(3);
        });
});
});

