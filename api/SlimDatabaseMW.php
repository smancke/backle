<?php

require 'Backlog.php';

class SlimDatabaseMW extends \Slim\Middleware
{
    protected $cfg;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }
    
    public function call()
    {
        $app = $this->app;
        
        $backlog = new Backlog();
        $backlog->setUser('testuser');
        $backlog->open($this->cfg);

        $app->cfg = $this->cfg;
        $app->backlog = $backlog;

        // Run inner middleware and application
        $this->next->call();

        $backlog->close();
    }
}