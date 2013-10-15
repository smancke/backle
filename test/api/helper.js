
function randomString() {
    return Math.random().toString(36).substring(2,7);
}

function createProxyURL(url) {
    return url.substring(url.indexOf('/api/') +1);
}

function PUT(url, data) {
    return $.ajax({
        url: url, async: false, type: 'PUT',
        data: JSON.stringify(data)
    })
}

function POST(url, data) {
    return $.ajax({
        url: url, async: false, type: 'POST',
        data: JSON.stringify(data)
    })
}

function POST_BACKLOGS(data) {
    return POST('/api/backlog', data);
}

function GET(url) {
    return $.ajax({
        url: url, async: false                
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

