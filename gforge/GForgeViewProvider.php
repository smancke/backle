<?php


class GForgeViewProvider extends ViewProvider {

    function __construct($app) {
        parent::__construct($app);
    }

    function getCommonJsIncludeFiles() {
        return ["/app/lib/jquery.min.js",
                "/app/lib/ui/jquery-ui.js", 
                "/app/lib/angular.min.js",
                "/app/lib/angular-resource.min.js"];
    }


    function writeHead($pageName, $jsIncludeFiles) {

        $group = group_get_object_by_name($this->app->backle->getProjectName());
        site_project_header(array('title'=>'Backle - agile backlog', 'h1' => '', 'group'=>$group->getID(), 'toptab' => 'backle',
                'submenu' => ''));

        // do javascript includes
        $jsIncludeFiles = array_merge($this->getCommonJsIncludeFiles(), $jsIncludeFiles);
        foreach ($jsIncludeFiles as $jsIncludeFile) {
            echo "    <script src=\"" . cfg_basepath() . $jsIncludeFile ."\"></script>\n";            
        }

        // set some javascript variables
        echo "    <script>\n";
	echo "         $('html').attr('ng-app', 'backle');\n";
        // include stylesheeds
 	echo "        $('head').append('<link rel=\"stylesheet\" href=\"". cfg_basepath() ."/app/backle.css\" type=\"text/css\"/>');\n";
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