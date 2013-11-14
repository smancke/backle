<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>backle - the agile backlog</title>

    <?php include('headSection.php') ?>

    <script src="<?=cfg_basepath()?>/app/projectStartpage.js"></script>
    <script src="<?=cfg_basepath()?>/app/common.js"></script>
  </head>
  <body ng-controller="ProjectIndexCtrl">
    <?php include('header.php') ?>
    
    <br>
    <div class="row" ng-show="true">
      <div class="col-md-5 col-md-offset-1">
        <a href="<?=cfg_basepath()?>/c/create?projectname=<?=$app->projectname?>" class="btn btn-default btn-lg">
          <span class="glyphicon glyphicon-plus"></span> Backlog
        </a>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-md-7 col-md-offset-1" id="item-list">
        
        <ul style="padding-left: 0px;" ng-repeat="backlog in backlogs">
          <li class="list-group-item"><i class="glyphicon glyphicon-chevron-right"></i> <a href="<?=cfg_basepath()?>/{{backlog.backlogname}}">{{backlog.backlogtitle}} (/{{backlog.backlogname}})</a></li>
        </ul>
      </div>
  </body>
</html>
