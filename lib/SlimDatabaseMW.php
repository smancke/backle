<?php


class SlimDatabaseMW extends \Slim\Middleware
{
    protected $cfg;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }

    
    public function call()
    {
        global $_COOKIE;

        $app = $this->app;
        $app->cfg = $this->cfg;

        $db = new dbFacile_mysql();
        $db->open($this->cfg['dbname'], $this->cfg['dbuser'], $this->cfg['dbpassword'], $this->cfg['dbhost']);
        if ($this->cfg['dblogfile'])
            $db->setLogile($this->cfg['dblogfile']);
              
        
        $app->backlog = new Backlog($db);

        $app->userMgr = new UserManager($db);
        if (isset($_COOKIE['s'])) {
            if ($app->userMgr->pickUpSession($_COOKIE['s'])) {
                $app->userInfo = $app->userMgr->getUserInfo();
                $app->backlog->setUserId($app->userInfo['id']);
            }
        }
        //$app->backlog->setUserId(1);

        // Run inner middleware and application
        $this->next->call();

        $db->close();
    }
}