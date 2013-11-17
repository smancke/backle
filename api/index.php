<?php

date_default_timezone_set("Europe/Berlin");
require 'Backlog.php';
require '../lib/dbFacile/dbFacile_mysql.php';
require '../lib/Slim/Slim.php';
require '../config.php';
require 'helper.php';
require_once '../app/SimpleOAuthLogin/UserManager.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->add(new \SlimDatabaseMW($cfg));
$app->add(new \AuthMW($cfg));
$app->response->headers->set('Content-Type', 'application/json');

// list all projects
$app->get('/project', function() use($app) {
        $projects = $app->userMgr->getMyOrPublicProjects();
        for ($i=0; $i<count($projects); $i++) {
            $backlogs[$i]['self'] = urlFor('project', ['backlogName' => $projects[$i]['name']]);
        }
        echo json_encode($projects);    
    })->name("projects");

//create a project with default backlog
$app->post('/project', function () use ($app) {
        if (! $app->userInfo) {
            authError("Login first!");
        }
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData || ! preg_match('/^[\w_-]+$/', $bodyData->name )) {
            userError("Parameter name ($bodyData->name) not valid (/^[\w_-]+$/).");
        }
        if ($app->userMgr->getProjectInfo($bodyData->name) || $bodyData->name == 'api' || $bodyData->name == 'c' || $bodyData->name == 'projects' ) {
            conflictError("Project name '".$bodyData->name."' already in use.");
        }
        
        $projectId = $app->userMgr->createProject($bodyData->name, $bodyData->title, $bodyData->is_public_viewable);
        $backlogId = $app->backlog->createBacklog($bodyData->name, $bodyData->title, $bodyData->is_public_viewable, $projectId, true);
        $app->response()->status(201);
        Header("Location: ". urlFor('project', ['projectname' => $bodyData->name]));
    });

$app->get('/project/:projectname', function($projectname) use ($app) {
        // TODO: check project read permissions
        $result = $app->userMgr->getProjectInfo($projectname);
        echo json_encode($result);
    })->name('project');


//create a backlog
$app->post('/project/:projectname/backlog', function ($projectname) use ($app) {
        if (! $app->userInfo) {
            authError("Login first!");
        }
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData || !property_exists($bodyData, 'backlogname') || ! preg_match('/^[\w_-]+$/', $bodyData->backlogname )) {
            userError("Parameter backlogname (" .($bodyData && property_exists($bodyData, 'backlogname') ? $bodyData->backlogname :''). ") not valid (/^[\w_-]+$/).");
        }
        if (!property_exists($bodyData, 'backlogtitle')) {
            userError("Parameter backlogtitle not supplied.");
        }
        if (!property_exists($bodyData, 'is_public_viewable')) {
            userError("Parameter is_public_viewable not supplied.");
        }
        if ($app->backlog->getBacklogIdByName($projectname, $bodyData->backlogname) || $bodyData->backlogname == 'api') {
            conflictError("Backlog name '".$bodyData->backlogname."' already in use.");
        }

        // find the project
        $project = $app->userMgr->getProjectInfo($projectname);
        if (!$project)
            notFoundError("Project '".$bodyData->projectname."' not found");
        // TODO: check owner rights for project
        $projectId = $project['id'];
        
        $id = $app->backlog->createBacklog($bodyData->backlogname, $bodyData->backlogtitle, $bodyData->is_public_viewable, $projectId);
        $app->response()->status(201);
        Header("Location: ". urlFor('stories', ['projectname' => $projectname,
                                                'backlogName' => $bodyData->backlogname]));
    });

// return the backlogs of a project 
$app->get('/project/:projectname/backlog', function($projectname) use ($app) {
        if (! $app->backlog->getRights($projectname)['read']) {
            notFoundError("Object not found: $projectname");
        }
        $backlogs = $app->backlog->getBacklogs($projectname);
        for ($i=0; $i<count($backlogs); $i++) {
            $backlogs[$i]['self'] = urlFor('stories', ['projectname' => $projectname,
                                                       'backlogName' => $backlogs[$i]['backlogname']
                                                       ]);
        }
        echo json_encode($backlogs);    
    })->name('backlogs');

// return a contents of a backlog 
$app->get('/project/:projectname/backlog/:backlogName', function($projectname, $backlogName) use ($app) {
        if (! $app->backlog->getRights($projectname)['read'] || ! $app->backlog->getBacklogIdByName($projectname, $backlogName) ){
            notFoundError("Object not found: $projectname/$backlogName");
        }
        $stories = $app->backlog->getItems($projectname, $backlogName);
        for ($i=0; $i<count($stories); $i++) {
            $stories[$i]['self'] = urlFor('item', ['projectname' => $projectname,
                                                   'backlogName' => $backlogName, 
                                                   'itemid' => $stories[$i]['id']]);
        }
        echo json_encode($stories);
    })->name('stories');

function getItem($app, $projectname, $backlogName,$id) {
    if (! $app->backlog->getRights($projectname)['read']) {
        notFoundError("Object not found: $projectname/$backlogName");
    }
    $item = $app->backlog->getItem($projectname, $backlogName,$id);
    if (!$item) {
        notFoundError("Object not found: $projectname/$backlogName");
    }
    return $item;        
}

$app->post('/project/:projectname/backlog/:backlogName', function ($projectname, $backlogName) use ($app) {
        if (! $app->backlog->getRights($projectname, $backlogName)['write']) {
            authError("No write permissions in: $projectname/$backlogName");
        }
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData) {
            userError("Posted data not valid.");
        }
        $id = $app->backlog->createItem($projectname, $backlogName, $bodyData);
        $app->response()->status(201);
        Header("Location: ". urlFor('item', ['projectname' => $projectname, 'backlogName' => $backlogName, 'itemid' => $id]));
        echo json_encode(getItem($app, $projectname, $backlogName, $id));
    });

$app->put('/project/:projectname/backlog/:backlogName/:itemid', function ($projectname, $backlogName, $itemid) use ($app) {
        if (! $app->backlog->getRights($projectname, $backlogName)['write']) {
            authError("No write permissions in: $projectname/$backlogName");
        }
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData) {
            userError("Posted data not valid.");
        }
        $app->backlog->updateItem($projectname, $backlogName, $itemid, $bodyData);
        echo json_encode(getItem($app, $projectname, $backlogName, $itemid));
    });

$app->put('/project/:projectname/backlog/:backlogName/:itemid/moveItemBehind', function ($projectname, $backlogName, $itemid) use ($app) {
        if (! $app->backlog->getRights($projectname, $backlogName)['write']) {
            authError("No write permissions in: $projectname/$backlogName");
        }
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData && ! preg_match('/^\d+$/', $bodyData->previousItem ) && $bodyData->previousItem != 'begin') {
            userError("Parameter previousItem not valid (/^\d+$/ or 'begin').");
        }
        if ($bodyData->previousItem == 'begin') {
            $app->backlog->moveItemToBegin($projectname, $backlogName, $itemid);
        } else {
            $app->backlog->moveItemBehind($projectname, $backlogName, $itemid, $bodyData->previousItem);
        }
        echo json_encode(getItem($app, $projectname, $backlogName, $itemid));
    });

$app->get('/project/:projectname/backlog/:backlogName/:itemid', wrap(function($projectname, $backlogName,$itemid) use($app) {
            if (! $app->backlog->getRights($projectname)['read']) {
                authError("No read permissions in: $projectname/$backlogName");
            }
            return getItem($app, $projectname, $backlogName, $itemid);
        }))->name('item');

$app->delete('/project/:projectname/backlog/:backlogName/:itemid', wrap(function($projectname, $backlogName,$itemid) use($app) {
            if (! $app->backlog->getRights($projectname, $backlogName)['write']) {
                authError("No write permissions in: $projectname/$backlogName");
            }
            $item = $app->backlog->deleteItem($projectname, $backlogName,$itemid);
        }))->name('deleteitem');

$app->run();
