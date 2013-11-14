
describe('Backlog api: ', function() {
    
    var projectName;
    var backlogName;
    self;

    beforeEach(function() {
        self = this;
        projectName = 'test-project-'+ randomString();
        backlogName = 'test-backlog-'+ randomString();
        
        login();
        createProject(projectName);
        createBacklog(projectName, backlogName);
    });
    
    describe('A backlog ressource', function(){

        it('should return the available backlogs', function() {
            GET('/api/project/'+projectName+'/backlog').done(function(data) {
                expect(data.length).toEqual(2);
                var createdBacklog = jQuery.grep(data, function (value) {
                    return value.backlogname == backlogName;
                });
                expect(createdBacklog.length).toBeGreaterThan(0);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });           
        });

        it('the default backlog should be available by the alias default', function() {
            GET('/api/project/'+projectName+'/backlog/default').done(function(data) {                
                expect(data.length).toEqual(0);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });

        
        it('should not allow using the same backlog name twice', function() {
            POST_BACKLOGS(projectName,
                          {backlogname: backlogName,
                           backlogtitle: 'bla',
                           is_public_viewable: true}

            ).done(function(data, textStatus, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });
        });

        it('should not allow to create backlogs with the name "api"', function() {
            POST_BACKLOGS(projectName,
                          {backlogname: "api",
                           backlogtitle: 'bla',
                           is_public_viewable: true}
            ).done(function(data, textStatus, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });
        });

    });
});

