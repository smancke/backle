
describe('Story api: ', function() {

    var backlogName;
    var BacklogManagement;
    var self;


    beforeEach(function() {
        self = this;
        backlogName = 'test-backlog-'+ randomString();
        
        POST_BACKLOGS(
            {backlogname: backlogName}
        ).fail(function(jqXHR, textStatus, errorThrown) {
            self.fail("failed with http status "+ jqXHR.status);
        });
    });
    
    describe('A backlog ressource', function(){

        it('should allow creation of a normal story', function() {
            POST('/api/backlog/'+ backlogName,
                 {
                     title: 'modify a user',                     
                     text: 'Als Produktverantwortlicher m\u00f6chte ich beliebige Stories in einem Backlog anlegen k\u00f6nnen, damit ich mir merken kann, was alles zu tun ist.',
                     detail: 'bla bla',
                     points: 13
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.title).toEqual('modify a user');
                expect(data.text).toEqual('Als Produktverantwortlicher m\u00f6chte ich beliebige Stories in einem Backlog anlegen k\u00f6nnen, damit ich mir merken kann, was alles zu tun ist.');

                expect(data.detail).toEqual('bla bla');
                expect(data.points).toEqual('13'); //TODO: This shoud be an integer
                expect(data.status).toEqual('open');                
                expect(data.backlogorder).toBeDefined();
                expect(data.created).toBeDefined(); //TODO: Better test of date types
                expect(data.changed).toBeNull(); 
                expect(data.done).toBeNull();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });

        it('should allow creation of an empty story', function() {
            POST('/api/backlog/'+ backlogName,
                 {
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.title).toEqual('');
                expect(data.text).toEqual('');

                expect(data.detail).toEqual('');
                expect(data.points).toEqual('0'); //TODO: This shoud be an integer
                expect(data.status).toEqual('open');                
                expect(data.backlogorder).toBeDefined();
                expect(data.created).toBeDefined(); //TODO: Better test of date types
                expect(data.changed).toBeNull(); 
                expect(data.done).toBeNull();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });

        it('should allow update of a story', function() {
            var storyUri;

            POST('/api/backlog/'+ backlogName,
                 {
                 }
            ).done(function(data, textStatus, jqXHR) {
                storyUri = jqXHR.getResponseHeader('Location');
            })
                   
            PUT(createProxyURL(storyUri),
                 {
                     title: 'modify a user',                     
                     text: 'Als Produktverantwortlicher ...',
                     detail: 'bla bla',
                     points: 13
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.title).toEqual('modify a user');
                expect(data.text).toEqual('Als Produktverantwortlicher ...');
                expect(data.detail).toEqual('bla bla');
                expect(data.points).toEqual('13'); //TODO: This shoud be an integer
                expect(data.status).toEqual('open');                
                expect(data.backlogorder).toBeDefined();
                expect(data.created).toBeDefined(); //TODO: Better test of date types
                expect(data.changed).toBeNull(); 
                expect(data.done).toBeNull();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });
    });
});


