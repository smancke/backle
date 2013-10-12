
describe('Backlog api: ', function() {
    
    var backlogName;
    var backlogUri;
    var self;

    beforeEach(function() {
        self = this;
        backlogName = 'test-backlog-'+ Math.random().toString(36).substring(2,7);
        
        POST(
            {backlogname: backlogName}
        ).done(function(data, textStatus, jqXHR) {
            expect(data.backlogname).toEqual(backlogName);
            backlogUri = jqXHR.getResponseHeader('Location');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            self.fail("failed with http status "+ jqXHR.status);
        });
    });
    
    describe('A backlog ressource', function(){
         
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
            ).done(function(data, textStatus, jqXHR) {
                self.fail("failed with http status "+ jqXHR.status);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                expect(jqXHR.status).toEqual(409); //conflict
            });
        });

        it('should not allow to create backlogs with the name "api"', function() {
            POST(
                {backlogname: "api"}
            ).done(function(data, textStatus, jqXHR) {
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

function GET() {
    return $.ajax({
        url: '/api/backlog', async: false                
    })
}

function GET_DATA(url) {
    var result;
    return $.ajax({
        url: url, async: false 
    }).done(function(data) {
        result = data;
    }).fail(function(jqXHR, textStatus, errorThrown) {
        expect("http get request with code 200").toEqual(jqXHR.status);
    });
    return result;
}
