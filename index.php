<?php

require 'lib/Slim/Slim.php';
require_once 'config.php';
$request = [
            'backlogname' => '',
            'storyid' => ''
           ];

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'cookies.encrypt' => true,
    'cookies.secret_key' => $cfg['cookie_secret'],
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    'cookies.path' => cfg_basepath() . '/'
));
$app->add(new \AuthMW($cfg));
$app->response->headers->set('Content-Type', 'text/html');


$app->get('/', function() use($app) {
        require 'app/startpage.php';
    });

$app->get('/c/login', function() use($app) {
        require 'app/login.php';
    });

$app->get('/c/logout', function() use($app) {
        $app->setCookie('backle_auth', '', time() - 1000000);
        $app->redirect(cfg_basepath() .'/c/login');
    });

$app->get('/c/create', function() use($app) {
        require 'app/create.php';
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


