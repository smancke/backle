
describe('Backlog api: ', function() {
    
    var backlogName;
    var self;

    beforeEach(function() {
        self = this;
        backlogName = 'test-backlog-'+ randomString();
        
        login();
        createBacklog(backlogName);
    });
    
    describe('A backlog ressource', function(){

        if('should be available by its uri', function() {
            //TODO: implement
        })
         
        it('should return the available backlogs', function() {
            GET('/api/backlog').done(function(data) {
                expect(data.length).toBeGreaterThan(0);
                var createdBacklog = jQuery.grep(data, function (value) {
                    return value.backlogname == backlogName;
                });
                expect(createdBacklog.length).toBeGreaterThan(0);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });           
        });
        
        it('should not allow using the same backlog name twice', function() {
            POST_BACKLOGS(
                {backlogname: backlogName}
            ).done(function(data, textStatus, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });
        });

        it('should not allow to create backlogs with the name "api"', function() {
            POST_BACKLOGS(
                {backlogname: "api"}
            ).done(function(data, textStatus, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });
        });

    });
});

