<?php

date_default_timezone_set("Europe/Berlin");
require '../lib/Slim/Slim.php';
require '../config.php';
require 'helper.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->add(new \SlimDatabaseMW($cfg));
$app->add(new \AuthMW($cfg));
$app->response->headers->set('Content-Type', 'application/json');

// list backlogs
$app->get('/backlog', function() use($app) {
        $backlogs = $app->backlog->getBacklogs();
        for ($i=0; $i<count($backlogs); $i++) {
            $backlogs[$i]['self'] = urlFor('stories', ['backlogName' => $backlogs[$i]['backlogname']]);
        }
        echo json_encode($backlogs);    
    })->name("backlogs");
   
//create a backlog
$app->post('/backlog', function () use ($app) {
        echo $app->request->getBody();
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData || ! preg_match('/^[\w_-]+$/', $bodyData->backlogname )) {
            userError("Parameter backlogname ($bodyData->backlogname) not valid (/^[\w_-]+$/).");
        }
        if ($app->backlog->getBacklogIdByName($bodyData->backlogname) || $bodyData->backlogname == 'api') {
            conflictError("Backlog name '".$bodyData->backlogname."' already in use.");
        }
        $id = $app->backlog->createBacklog($bodyData->backlogname);
        $app->response()->status(201);
        Header("Location: ". urlFor('stories', ['backlogName' => $bodyData->backlogname]));
    });

$app->get('/backlog/:backlogName', function($backlogName) use ($app) {
        if (! $app->backlog->isReadeableForUser($backlogName)) {
            notFoundError("Object not found: $backlogName");
        }
        $stories = $app->backlog->getItems($backlogName);
        for ($i=0; $i<count($stories); $i++) {
            $stories[$i]['self'] = urlFor('item', ['backlogName' => $backlogName, 'itemid' => $stories[$i]['id']]);
        }
        echo json_encode($stories);
    })->name('stories');

function getItem($app, $backlogName,$id) {
    $item = $app->backlog->getItem($backlogName,$id);
    if (!$item) {
        notFoundError("Object not found: $backlogName");
    }
    $item['link_backlog'] = urlFor('stories', ['backlogName' => $backlogName]);
    return $item;        
}

$app->post('/backlog/:backlogName', function ($backlogName) use ($app) {
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData) {
            userError("Posted data not valid.");
        }
        $id = $app->backlog->createItem($backlogName, $bodyData);
        $app->response()->status(201);
        Header("Location: ". urlFor('item', ['backlogName' => $backlogName, 'itemid' => $id]));
        echo json_encode(getItem($app, $backlogName, $id));
    });

$app->put('/backlog/:backlogName/:itemid', function ($backlogName, $itemid) use ($app) {
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData) {
            userError("Posted data not valid.");
        }
        $app->backlog->updateItem($backlogName, $itemid, $bodyData);
        echo json_encode(getItem($app, $backlogName, $itemid));
    });

$app->put('/backlog/:backlogName/:itemid/moveItemBehind', function ($backlogName, $itemid) use ($app) {
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData && ! preg_match('/^\d+$/', $bodyData->previousItem ) && $bodyData->previousItem != 'begin') {
            userError("Parameter previousItem not valid (/^\d+$/ or 'begin').");
        }
        if ($bodyData->previousItem == 'begin') {
            $app->backlog->moveItemToBegin($backlogName, $itemid);
        } else {
            $app->backlog->moveItemBehind($backlogName, $itemid, $bodyData->previousItem);
        }
        echo json_encode(getItem($app, $backlogName, $itemid));
    });

$app->get('/backlog/:backlogName/:itemid', wrap(function($backlogName,$itemid) use($app) {
            return getItem($app, $backlogName, $itemid);
        }))->name('item');

$app->delete('/backlog/:backlogName/:itemid', wrap(function($backlogName,$itemid) use($app) {
            $item = $app->backlog->deleteItem($backlogName,$itemid);
        }))->name('deleteitem');


$app->get('/', 
          value(["backlogs" => urlFor("backlogs")])
          )->name('index');

$app->run();
