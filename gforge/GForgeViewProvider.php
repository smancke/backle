<?php

class GForgeViewProvider extends ViewProvider {

    function __construct($app) {
        parent::__construct($app);
    }

    function writePageHeader() {
        echo "<h1>gforge header: ". $this->app->backle->getProjectName() ."</h1>";
    }

}