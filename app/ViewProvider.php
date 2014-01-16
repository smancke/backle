<?php

class ViewProvider {

    protected $app;

    function __construct($app) {
        $this->app = $app;
    }

    function getCommonJsIncludeFiles() {
        return ["/app/lib/jquery.min.js",
                "/app/lib/ui/jquery-ui.js", 
                "/app/lib/bootstrap.min.js",
                "/app/lib/angular.min.js",
                "/app/lib/angular-resource.min.js"];
    }

    function writeHead($pageName, $jsIncludeFiles) {
        echo "<!DOCTYPE html>\n";
        echo "<html ng-app=\"backle\">\n";
        echo "  <head>\n";
        echo "    <title>Backlog - $pageName</title>\n";
        echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";

        // include stylesheeds
        echo "    <link rel=\"stylesheet\" href=\"". cfg_basepath() ."/app/backle.css\" type=\"text/css\"/>\n";
        echo "    <link href=\"" . cfg_basepath() . "/app/lib/bootstrap.css\" rel=\"stylesheet\" media=\"screen\"/>\n";

        // do javascript includes
        $jsIncludeFiles = array_merge($this->getCommonJsIncludeFiles(), $jsIncludeFiles);
        foreach ($jsIncludeFiles as $jsIncludeFile) {
            echo "    <script src=\"" . cfg_basepath() . $jsIncludeFile ."\"></script>\n";            
        }

        // set some javascript variables
        echo "    <script>\n";
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
    
        echo "  </head>\n";
        echo "  <body style=\"overflow-y:auto\">\n";
    
    }

    function writePageHeader() {
        include('header.php');
    }

    function writePageFooter() {
    	echo "    </body>\n";
    	echo "</html>\n";
    }

}