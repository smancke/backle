
describe('Story api: ', function() {
    
    describe('A backlog ressource', function(){

        var backlogName;
        var BacklogManagement;
        var self;

        beforeEach(function() {
            self = this;
            backlogName = 'test-backlog-'+ Math.random().toString(36).substring(2,7);

            POST(
                {backlogname: backlogName}
            ).done(function(data, jqXHR) {
                expect(data.backlogname).toEqual(backlogName);
                expect(data.backlogname).toEqual(backlogName);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });
        });
         
        it('should return the available backlogs', function() {
            GET().done(function(data) {
                expect(data.length).toBeGreaterThan(0);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                self.fail("failed with http status "+ jqXHR.status);
            });           
        });
        
        it('should not allow using the same backlog name twice', function() {
            POST(
                {backlogname: backlogName}
            ).done(function(data, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });
        });

        it('should not allow to create backlogs with the name "api"', function() {
            POST(
                {backlogname: "api"}
            ).done(function(data, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });            
        });

    });
});

function POST(data) {
    return $.ajax({
        url: '/api/backlog', async: false, type: 'POST',
        data: JSON.stringify(data)
    })
}

function GET(data) {
    return $.ajax({
        url: '/api/backlog', async: false                
    })
}
