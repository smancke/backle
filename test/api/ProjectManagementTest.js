
describe('Project api: ', function() {
    
    var projectName;
    self;

    beforeEach(function() {
        self = this;
        projectName = 'test-project-'+ randomString();
        
        login();
    });
    
    describe('The project api', function(){

        it('should be possible to create projects', function() {
            createProject(projectName);
            
            GET('/api/project/'+projectName).done(function(data) {
                expect(data['name']).toEqual(projectName);
                expect(data['title']).not.toBeNull();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });           
        });

        it('a project shoud have one default backlog', function() {
            var title = 'Title for project '+projectName;
            POST_PROJECTS(
                {name: projectName,
                 title: title,
                 is_public_viewable: true}
            ).fail(function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status != 201) {
                    self.fail("failed with http status "+ jqXHR.status);
                }
            });    
            
            GET('/api/project/'+projectName+'/backlog').done(function(data) {                
                expect(data.length).toEqual(1); // one backlog
                expect(data[0]['backlogname']).toEqual(projectName);
                expect(data[0]['backlogtitle']).toEqual(title);
                expect(data[0]['is_project_default']).toEqual('1');
                expect(data[0]['is_public_viewable']).toEqual('1');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });

        it('a project shoud be made invisible', function() {
            var title = 'Title for project '+projectName;
            POST_PROJECTS(
                {name: projectName,
                 title: title,
                 is_public_viewable: false}
            ).fail(function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status != 201) {
                    self.fail("failed with http status "+ jqXHR.status);
                }
            });    
            
            GET('/api/project/'+projectName).done(function(data) {
                expect(data['is_public_viewable']).toEqual('0');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });           

            GET('/api/project/'+projectName+'/backlog').done(function(data) {                
                expect(data[0]['is_public_viewable']).toEqual('0');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });
    });
});

