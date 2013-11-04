<?php
date_default_timezone_set("Europe/Berlin");

require_once 'app/SimpleOAuthLogin/GoogleLoginProvider.php';
require_once 'app/SimpleOAuthLogin/LoginHandler.php';
require_once 'app/SimpleOAuthLogin/UserManager.php';
require 'api/Backlog.php';
require 'lib/dbFacile/dbFacile_mysql.php';
require 'lib/Slim/Slim.php';
require_once 'config.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array());
$app->add(new \SlimDatabaseMW($cfg));
$app->add(new \AuthMW($cfg));
$app->response->headers->set('Content-Type', 'text/html');


$app->get('/', function() use($app) {
        require 'app/startpage.php';
    });

$app->get('/c/loginRedirect', function() use($app) {
        var_dump($app->cfg);
        $googleLogin = new GoogleLoginProvider($app->cfg['google']);
        $loginHandler = new LoginHandler($googleLogin);
        $loginHandler->redirectToAuthorisationServer();
        die();
    });

$app->get('/c/login', function() use($app) {
        global $_GET;
        $googleLogin = new GoogleLoginProvider($app->cfg['google']);
        $loginHandler = new LoginHandler($googleLogin);
        if (isset($_GET['code']) 
            && $loginHandler->login()
            && $loginHandler->ensureUserAndStartSession($app->userMgr)
            && $loginHandler->updateMyCircles($app->userMgr)
            ) {
            
            Header('Location: '.$loginHandler->getNextAction(cfg_basepath().'/'));
            die();
        } else {
            $errorMessage = $loginHandler->getError();
            require 'app/login.php';
        }
    });

$app->map('/c/demoLogin', function() use($app) {
        $errorMessage = '';
        if (! (isset($app->cfg['demo_login_enabled']) && $app->cfg['demo_login_enabled'])) {
            $errorMessage = 'Demo Login not activated!';
        } else {
            if ($app->request()->params('demo_login_password')) {
                if ($app->request()->params('demo_login_password') == $app->cfg['demo_login_password']) {
                    
                    $app->userMgr->setAndCreateUserIfNotExists('demo', 'demo', 'demo', 'Demo User', Null);
                    $sessionId = $app->userMgr->startSession();
                    setcookie('s', $sessionId, 0, '/');            
                    
                    Header('Location: '.cfg_basepath().'/');
                    die();
                } else {
                    $errorMessage = 'Demo password is wrong!';
                }
            }
        }
        require 'app/demoLogin.php';
    })->via('GET', 'POST');


$app->get('/c/logout', function() use($app) {
        $loginHandler = new LoginHandler(null);
        $loginHandler->logout($app->userMgr);

        Header('Location: '.cfg_basepath().'/');
        die();
    });

$app->get('/c/create', function() use($app) {
        require 'app/create.php';
    });

$app->get('/:backlog', function($backlogname) use($app) {
        $app->backlogname = $backlogname;
        require 'app/list.php';
    });

$app->get('/:backlog/:story', function($backlogname, $storyid) use($app) {
        $app->backlogname = $backlogname;
        $app->storyid = $storyid;
        require 'app/detail.php';
    });


$app->run();


