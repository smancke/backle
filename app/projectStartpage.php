<?php

$app->backle->writeHead('the agile backlog',
                              ['/app/projectStartpage.js', '/app/common.js']);

$app->backle->writePageHeader();

?>

<div ng-controller="ProjectIndexCtrl">    
    <br>
    <div class="row" ng-show="true">
      <div class="col-md-5 col-md-offset-1">
        <a href="<?=cfg_basepath()?>/c/create?projectname=<?=$app->backle->getProjectName()?>" class="btn btn-default btn-lg">
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
</div>    
  </body>
</html>
