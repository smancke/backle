    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="<?=cfg_basepath()?>/app/lib/jquery.min.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/ui/jquery-ui.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/bootstrap.min.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/angular.min.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/angular-resource.min.js"></script>

    <link rel="stylesheet" href="<?=cfg_basepath()?>/app/backle.css" type="text/css"/>
    <link href="<?=cfg_basepath()?>/app/lib/bootstrap.css" rel="stylesheet" media="screen">
    <script>
<?php 
$backlogName = $app->backlogname ? $app->backlogname : $app->request->params('backlogname');
$projectName = $app->projectname ? $app->projectname : $app->request->params('projectname');
echo "        global_backlog_permissions = ". json_encode($app->backlog->getRights($projectName)) .";\n";
echo "        global_projectname = '" . $projectName ."';\n";
echo "        global_backlogname = '" .  ($backlogName) ."';\n";
echo "        global_storyid = '" . ($app->storyid ? $app->storyid : $app->request->params('storyid')) ."';\n";
echo "        global_basepath = '". cfg_basepath() ."';\n";
if ($projectName) {
    echo "        global_backlog_basepath = '". cfg_basepath() ."/api/project/$projectName/backlog/$backlogName';\n";
}
//        echo "        global_user = '". ($app->user != null && property_exists($app->user, 'user')) ? $app->user->username : '' ."';\n";
     ?>
    </script>
