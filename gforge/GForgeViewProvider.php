<?php

class GForgeViewProvider extends ViewProvider {

    function __construct($app) {
        parent::__construct($app);
    }

    function writeHead($pageName, $jsIncludeFiles) {

     	// disable gforge html syntax checks
	global $sysdebug_xmlstarlet;
        $sysdebug_xmlstarlet = false;


        $group = group_get_object_by_name($this->app->backle->getProjectName());
	//$group->getID()
	site_project_header(array('title'=>'backle', 'h1' => '', 'group'=>23, 'toptab' => 'home',
                'submenu' => ''));

    //        echo "<h1>gforge header: ". $this->app->backle->getProjectName() ."</h1>";




        // include stylesheeds
//        echo "    <link rel=\"stylesheet\" href=\"". cfg_basepath() ."/app/backle.css\" type=\"text/css\"/>\n";
//        echo "    <link href=\"" . cfg_basepath() . "/app/lib/bootstrap.css\" rel=\"stylesheet\" media=\"screen\"/>\n";

        // do javascript includes
        $jsIncludeFiles = array_merge($this->getCommonJsIncludeFiles(), $jsIncludeFiles);
        foreach ($jsIncludeFiles as $jsIncludeFile) {
            echo "    <script src=\"" . cfg_basepath() . $jsIncludeFile ."\"></script>\n";            
        }

        // set some javascript variables
        echo "    <script>\n";
	echo " $.noConflict();";
        $projectName = $this->app->backle->getProjectName();
        $backlogName = $this->app->backle->getBacklogName();
        $storyId = $this->app->backle->getStoryId();
        echo "        global_backlog_permissions = ". json_encode($this->app->backlog->getRights($projectName)) .";\n";
        echo "        global_projectname = '$projectName';\n";
        echo "        global_backlogname = '$backlogName';\n";
        echo "        global_storyid = '$storyId';\n";
        echo "        global_basepath = '". cfg_basepath() ."';\n";
        if ($projectName) {
            echo "        global_backlog_basepath = '". cfg_basepath() ."/api/project/$projectName/backlog/$backlogName';\n";
        }
        echo "    </script>\n";
    }

    function writePageHeader() {
    }

    function writePageFooter() {
        site_project_footer(array());
    }
}