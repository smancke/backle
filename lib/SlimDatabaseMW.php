<?php

require 'Backlog.php';
require 'dbFacile/dbFacile_mysql.php';

class SlimDatabaseMW extends \Slim\Middleware
{
    protected $cfg;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }

    
    public function call()
    {
        $app = $this->app;

        $db = new dbFacile_mysql();
        $db->open($this->cfg['dbname'], $this->cfg['dbuser'], $this->cfg['dbpassword'], $this->cfg['dbhost']);
        if ($this->cfg['dblogfile'])
            $db->setLogile($this->cfg['dblogfile']);
              
        $backlog = new Backlog($db);
        //$backlog->setAndCreateUserIfNotExists($app->user->username, $app->user->origin);
        $backlog->setAndCreateUserIfNotExists('testuser', 'nowhere');

        $app->cfg = $this->cfg;
        $app->backlog = $backlog;

        // Run inner middleware and application
        $this->next->call();

        $db->close();
    }
}