<?php

class GForgeViewProvider extends ViewProvider {

    function __construct($app) {
        parent::__construct($app);
    }

    function writePageHeader() {
        $group = group_get_object_by_name($this->app->backle->getProjectName());
	//$group->getID()
	site_project_header(array('title'=>'backle', 'h1' => '', 'group'=>23, 'toptab' => 'home',
                'submenu' => ''));

    //        echo "<h1>gforge header: ". $this->app->backle->getProjectName() ."</h1>";
    }

}