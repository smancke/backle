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
if (isset($cfg['embedded_in_gforge']) && $cfg['embedded_in_gforge']) {
    $app->add(new \GForgeAuthMW($cfg));
} else {
    $app->add(new \AuthMW($cfg));
}
$app->add(new \SlimDatabaseMW($cfg));
$app->response->headers->set('Content-Type', 'text/html');


$app->get('/', function() use($app) {
        require 'app/startpage.php';
    });

$app->get('/projects/:projectname', function($projectname) use($app) {
        $app->projectname = $projectname;
        require 'app/projectStartpage.php';
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
        if (!$app->userInfo) {
            Header('Location: '.cfg_basepath().'/c/loginRedirect?nextAction='.cfg_basepath().'/c/create');
            die();
        }
        require 'app/create.php';
    });

$app->get('/:project', function($projectname) use($app) {
        $app->projectname = $projectname;
        $app->backlogname = 'default';
        require 'app/list.php';
    });

$app->get('/:project/backlog/:backlogname', function($projectname, $backlogname) use($app) {
        $app->projectname = $projectname;
        $app->backlogname = $backlogname;
        require 'app/list.php';
    });

$app->get('/:project/:story', function($projectname, $storyid) use($app) {
        $app->projectname = $projectname;
        $app->backlogname = 'default';
        $app->storyid = $storyid;
        require 'app/detail.php';
    });

$app->get('/:project/backlog/:backlogname/:story', function($projectname, $backlogname, $storyid) use($app) {
        $app->projectname = $projectname;
        $app->backlogname = $backlogname;
        $app->storyid = $storyid;
        require 'app/detail.php';
    });


$app->run();


