<?php


class SlimDatabaseMW extends \Slim\Middleware
{
    protected $cfg;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }

    
    public function call()
    {
        $app = $this->app;
        $app->cfg = $this->cfg;

        $app->db = new dbFacile_mysql();
        $app->db->open($this->cfg['dbname'], $this->cfg['dbuser'], $this->cfg['dbpassword'], $this->cfg['dbhost']);
        if ($this->cfg['dblogfile'])
            $app->db->setLogile($this->cfg['dblogfile']);
                     
        $app->backlog = new Backlog($app->db);

        // Run inner middleware and application
        $this->next->call();

        $app->db->close();
    }
}