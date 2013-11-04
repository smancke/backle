
describe('Story api: ', function() {

    var backlogName;
    var BacklogManagement;
    var self;

    beforeEach(function() {
        self = this;
        backlogName = 'test-backlog-'+ randomString();

        login();
        createBacklog(backlogName);
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
                expect(data.type).toEqual('story');
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
                expect(data.title).toEqual(null);
                expect(data.text).toEqual(null);

                expect(data.detail).not.toEqual(null);
                expect(data.points).toEqual(null); //TODO: This shoud be an integer
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

            // update of all fields
            PUT(createProxyURL(storyUri),
                 {
                     title: 'modify a user',                     
                     text: 'Als Produktverantwortlicher ...',
                     detail: 'bla bla',
                     points: 13,
                     status: 'done'
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.title).toEqual('modify a user');
                expect(data.text).toEqual('Als Produktverantwortlicher ...');
                expect(data.detail).toEqual('bla bla');
                expect(data.points).toEqual('13'); //TODO: This shoud be an integer
                expect(data.status).toEqual('done');                
                expect(data.backlogorder).toBeDefined();
                expect(data.created).toBeDefined(); //TODO: Better test of date types

                // check update of date fields
                expect(data.changed).not.toBeNull(); 
                expect(data.done).not.toBeNull();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });


            // verify, that sending no fields has no impact
            PUT(createProxyURL(storyUri),
                 {}
            ).done(function(data, textStatus, jqXHR) {
                expect(data.title).toEqual('modify a user');
                expect(data.text).toEqual('Als Produktverantwortlicher ...');
                expect(data.detail).toEqual('bla bla');
                expect(data.points).toEqual('13'); //TODO: This shoud be an integer
                expect(data.status).toEqual('done');                
                expect(data.backlogorder).toBeDefined();
                expect(data.created).toBeDefined(); //TODO: Better test of date types

                // check update of date fields
                expect(data.changed).not.toBeNull(); 
                expect(data.done).not.toBeNull();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });

            // verify, that done date is resetet on state change!='done'
            PUT(createProxyURL(storyUri),
                {status:'open'}
            ).done(function(data, textStatus, jqXHR) {
                expect(data.status).toEqual('open');                
                expect(data.done).toBeNull();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });


        it('should allow creation of a release item', function() {
            POST('/api/backlog/'+ backlogName,
                 {
                     title: 'Sprint #2',                     
                     type: 'milestone'
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.type).toEqual('milestone');
                expect(data.title).toEqual('Sprint #2');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });

        it('should allow story point update of an milestone item to "0"', function() {
            var storyUri;

            POST('/api/backlog/'+ backlogName,
                 {
                     title: 'Sprint #2',                     
                     type: 'milestone'
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.points).toBeNull();
                storyUri = jqXHR.getResponseHeader('Location');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });

            PUT(createProxyURL(storyUri),
                 {
                     points: 0,
                 }
            ).done(function(data, textStatus, jqXHR) {
                expect(data.points).toEqual('0');
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });

        });

        it('should allow fetching all the stories of the backlog', function() {
            for (var i=0; i<3; i++) {
                POST('/api/backlog/'+ backlogName,
                     {
                         title: 'modify a user',                     
                         text: 'Als Produktverantwortlicher ...',
                         detail: 'bla bla',
                         points: 13
                     }
                    ).done(function() {
                        GET('/api/backlog/'+ backlogName)
                            .done(function(data, textStatus, jqXHR) {
                                expect(data.length).toEqual(i+1);
                                expect(data[0].type).toEqual('story');
                                expect(data[0].title).toEqual('modify a user');
                                expect(data[0].text).toEqual('Als Produktverantwortlicher ...');
                                expect(data[0].detail).not.toBeDefined(); //no detail in list
                                expect(data[0].points).toEqual('13'); //TODO: This shoud be an integer
                                expect(data[0].status).toEqual('open');                
                                expect(data[0].backlogorder).toBeDefined();
                                expect(data[0].created).toBeDefined(); //TODO: Better test of date types
                                expect(data[0].changed).toBeNull(); 
                                expect(data[0].done).toBeNull();                    
                            }).fail(function(jqXHR, textStatus, errorThrown) {
                                self.fail("failed with http status "+ jqXHR.status);
                            });
                    });                
            }
        });

        function createABunchOfStories(count) {
            for(var i=0; i<count; i++) {
                POST('/api/backlog/'+ backlogName, {});
            }
        }

        it('should allow moving a story from position 3 to begin', function() {
            var storyUri;
            createABunchOfStories(3);
            GET('/api/backlog/'+ backlogName)            
                .done(function(initialList, textStatus, jqXHR) {
                    movingId = initialList[2].id;

                    PUT(createProxyURL(initialList[2].self +'/moveItemBehind'),
                         {previousItem: 'begin'}).done(function() {
                             
                             GET('/api/backlog/'+ backlogName)            
                                 .done(function(newList, textStatus, jqXHR) {
                                     expect(movingId).toEqual(newList[0].id);                                    
                                 }).fail(function(jqXHR, textStatus, errorThrown) {
                                     self.fail("failed with http status "+ jqXHR.status);
                                 });
                         }).fail(function(jqXHR, textStatus, errorThrown) {
                             self.fail("failed with http status "+ jqXHR.status);
                         });                                                                          
                }).fail(function(jqXHR, textStatus, errorThrown) {
                             self.fail("failed with http status "+ jqXHR.status);
                });            
        });



        it('should allow moving a story from position 5 to position 3', function() {
            var storyUri;
            createABunchOfStories(6);
            GET('/api/backlog/'+ backlogName)            
                .done(function(initialList, textStatus, jqXHR) {
                    movingId = initialList[4].id;
                    targetId = initialList[1].id;

                    PUT(createProxyURL(initialList[4].self +'/moveItemBehind'),
                         {previousItem: targetId}).done(function() {
                             
                             GET('/api/backlog/'+ backlogName)            
                                 .done(function(newList, textStatus, jqXHR) {
                                     expect(movingId).toEqual(newList[2].id);
                                     expect(initialList[0].id).toEqual(newList[0].id);
                                     expect(initialList[1].id).toEqual(newList[1].id);
                                     expect(initialList[2].id).toEqual(newList[3].id);
                                     expect(initialList[3].id).toEqual(newList[4].id);
                                     expect(initialList[4].id).toEqual(newList[2].id);
                                     expect(initialList[5].id).toEqual(newList[5].id);

                                 }).fail(function(jqXHR, textStatus, errorThrown) {
                                     self.fail("failed with http status "+ jqXHR.status);
                                 });
                         }).fail(function(jqXHR, textStatus, errorThrown) {
                             self.fail("failed with http status "+ jqXHR.status);
                         });                                                                          
                }).fail(function(jqXHR, textStatus, errorThrown) {
                             self.fail("failed with http status "+ jqXHR.status);
                });            
        });

    });
});
