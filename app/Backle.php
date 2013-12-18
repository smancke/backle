<?php

require_once 'ViewProvider.php';

class Backle {

    protected $app;
    protected $cfg;
    protected $viewProvider;

    protected $projectName;
    protected $backlogName;
    protected $storyId;

    function __construct($app, $cfg) {
        $this->app = $app;
        $this->cfg = $cfg;
    }

    function setup() {
        $this->viewProvider = new ViewProvider($this->app);
    }

    function createSlimAuthMiddleware() {
        return new \AuthMW($this->cfg);
    }

    function writeHead($pageName, $jsIncludeFiles) {
        $this->viewProvider->writeHead($pageName, $jsIncludeFiles);
    }

    function writePageHeader() {
        $this->viewProvider->writePageHeader();
    }


    function setProjectName($projectName) {
        $this->projectName = $projectName;
    }

    function getProjectName() {
        if (! $this->projectName) {
            return $this->app->request->params('projectname');
        } 
        return $this->projectName;                
    }

    function setBacklogName($backlogName) {
        $this->backlogName = $backlogName;
    }

    function getBacklogName() {
        if (! $this->backlogName) {
            if ($this->app->request->params('backlogname')) {
                return $this->app->request->params('backlogname');
            } 
            return 'default';
        }
        return $this->backlogName;
    }

    function setStoryId($storyId) {
        $this->storyId = $storyId;
    }

    function getStoryId() {
        if (! $this->storyId) {
            return $this->app->request->params('storyid');
        } 
        return $this->storyId;                
    }


    function getApp() {
        return $this->app;
    }

}

