<?php

class GForgeAuthMW extends \Slim\Middleware
{
    protected $cfg;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }
    
    public function call()
    {
        $cookieValueJson = $this->app->getCookie('backle_auth');
        if ($cookieValueJson) {
            $cookieValue = json_decode($cookieValueJson);
            $this->app->user = $cookieValue;
        } else {
            $this->app->user = null;
        }

        // Run inner middleware and application
        $this->next->call();
    }
}