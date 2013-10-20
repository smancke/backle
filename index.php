<?php

require 'lib/Slim/Slim.php';
require_once 'config.php';
$request = [
            'backlogname' => '',
            'storyid' => ''
           ];

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//$app->add(new \SlimDatabaseMW($cfg));
$app->response->headers->set('Content-Type', 'text/html');


$app->get('/', function() use($app) {
        require 'app/startpage.php';
    });

$app->get('/:backlog', function($backlogname) use($app) {
        global $request;
        $request['backlogname'] = $backlogname;
        require 'app/list.php';
    });

$app->get('/:backlog/:story', function($backlogname, $storyid) use($app) {
        global $request;
        $request['backlogname'] = $backlogname;
        $request['storyid'] = $storyid;
        require 'app/detail.php';
    });


$app->run();


