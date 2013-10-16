<?php

require 'Slim/Slim.php';
require 'config.php';
require 'helper.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->add(new \SlimDatabaseMW($cfg));
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
        $stories = $app->backlog->getStories($backlogName);
        for ($i=0; $i<count($stories); $i++) {
            $stories[$i]['self'] = urlFor('story', ['backlogName' => $backlogName, 'storyid' => $stories[$i]['id']]);
        }
        echo json_encode($stories);
    })->name('stories');

$app->post('/backlog/:backlogName', function ($backlogName) use ($app) {
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData) {
            userError("Posted data not valid.");
        }
        $id = $app->backlog->createStory($backlogName, $bodyData);
        $app->response()->status(201);
        Header("Location: ". urlFor('story', ['backlogName' => $backlogName, 'storyid' => $id]));

        $story = $app->backlog->getStory($backlogName,$id);
        $story['link_backlog'] = urlFor('stories', ['backlogName' => $backlogName]);
        echo json_encode($story);        
    });

$app->put('/backlog/:backlogName/:storyid', function ($backlogName, $storyid) use ($app) {
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData) {
            userError("Posted data not valid.");
        }
        $id = $app->backlog->updateStory($backlogName, $storyid, $bodyData);
    });

$app->put('/backlog/:backlogName/:storyid/moveStoryBehind', function ($backlogName, $storyid) use ($app) {
        $bodyData = json_decode($app->request->getBody());
        if (!$bodyData && ! preg_match('/^\d+$/', $bodyData->previousStory ) && $bodyData->previousStory != 'begin') {
            userError("Parameter previousStory not valid (/^\d+$/ or 'begin').");
        }
        if ($bodyData->previousStory == 'begin') {
            $app->backlog->moveStoryToBegin($backlogName, $storyid);
        } else {
            $app->backlog->moveStoryBehind($backlogName, $storyid, $bodyData->previousStory);
        }
    });


$app->get('/backlog/:backlogName/:storyid', wrap(function($backlogName,$storyid) use($app) {
            $story = $app->backlog->getStory($backlogName,$storyid);
            if (!$story) {
                notFoundError("Object not found: $backlogName");
            }
            $story['link_backlog'] = urlFor('stories', ['backlogName' => $backlogName]);
            return $story;
        }))->name('story');

$app->delete('/backlog/:backlogName/:storyid', wrap(function($backlogName,$storyid) use($app) {
            $story = $app->backlog->deleteStory($backlogName,$storyid);
        }))->name('deletestory');


$app->get('/', 
          value(["backlogs" => urlFor("backlogs")])
          )->name('index');

$app->run();
