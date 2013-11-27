<?php

class AuthMW extends \Slim\Middleware
{
    protected $cfg;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }
    
    public function call()
    {
        global $_COOKIE;

        $app = $this->app;

        $app->userMgr = new UserManager($app->db);
        if (isset($_COOKIE['s'])) {
            if ($app->userMgr->pickUpSession($_COOKIE['s'])) {
                $app->userInfo = $app->userMgr->getUserInfo();
                $app->backlog->setUserId($app->userInfo['id']);
            }
        }

        // Run inner middleware and application
        $this->next->call();
    }
}