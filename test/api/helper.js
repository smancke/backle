
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

function POST_BACKLOGS(projectName, data) {
    return POST('/api/project/'+projectName + '/backlog', data);
}

function POST_PROJECTS(data) {
    return POST('/api/project', data);
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

function login() {
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
    
}

function createBacklog(projectName, backlogName) {
    // create backlog
    POST_BACKLOGS(projectName,
                  {backlogname: backlogName,
                   backlogtitle: 'Title for '+backlogName,
                   is_public_viewable: true}
                 ).fail(function(jqXHR, textStatus, errorThrown) {
                     if (jqXHR.status != 201) {
                         self.fail("failed with http status "+ jqXHR.status);
                     }
                 });    
}

function createProject(projectName) {
    POST_PROJECTS(
        {name: projectName,
         title: 'Title for project '+projectName,
         is_public_viewable: true}
    ).fail(function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.status != 201) {
            self.fail("failed with http status "+ jqXHR.status);
        }
    });    
}
