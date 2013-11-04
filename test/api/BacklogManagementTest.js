
describe('Backlog api: ', function() {
    
    var backlogName;
    var backlogUri;
    var self;

    beforeEach(function() {
        self = this;
        backlogName = 'test-backlog-'+ Math.random().toString(36).substring(2,7);

        // login
        $.ajax({
            url: '/c/demoLogin',
            type: "POST",
            data: "demo_login_password=secret",
            async: false,
        }).always(function(data, textStatus, jqXHR) { 
            if (jqXHR.status != 200 && jqXHR.status != 301 && jqXHR.status != 302) {
                self.fail("failed to login with demo account "+ jqXHR.status);
            }
        });      

        // create backlog
        POST_BACKLOGS(
            {backlogname: backlogName,
             backlogtitle: 'Title for '+backlogName,
             is_public_viewable: true}
        ).done(function(data, textStatus, jqXHR) {
            expect(data.backlogname).toEqual(backlogName);
            backlogUri = jqXHR.getResponseHeader('Location');            
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status != 201) {
                self.fail("failed with http status "+ jqXHR.status);
            }
        });
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

