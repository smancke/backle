    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="<?=cfg_basepath()?>/app/backle.css" type="text/css"/>
    <link href="<?=cfg_basepath()?>/app/lib/bootstrap.css" rel="stylesheet" media="screen">

    <script src="<?=cfg_basepath()?>/app/lib/jquery.min.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/ui/jquery-ui.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/angular.min.js"></script>
    <script src="<?=cfg_basepath()?>/app/lib/angular-resource.min.js"></script>

    <script>
<?php 
         global $request;

        echo "        global_backlogname = '" . $request['backlogname'] ."';\n";
        echo "        global_storyid = '" . $request['storyid'] ."';\n";
        echo "        global_basepath = '". cfg_basepath() ."';\n";
     ?>
    </script>
