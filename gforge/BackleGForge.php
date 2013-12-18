<?php

require_once "GForgeAuthMW.php";
require_once "GForgeViewProvider.php";

class BackleGForge extends Backle {

    protected $authMiddleware;

    function __construct($app, $cfg) {
        parent::__construct($app, $cfg);
    }

    function setup() {
        $this->viewProvider = new GForgeViewProvider($this->app);
    }

    function createSlimAuthMiddleware() {
        $this->authMiddleware = new \GForgeAuthMW($this->cfg);
        return $this->authMiddleware;
    }

    function setProjectName($projectName) {
        parent::setProjectName($projectName);

        $this->authMiddleware->setProjectName($projectName);
    }
}
