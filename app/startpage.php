<?php

$app->backle->writeHead('the agile backlog',
                              ['/app/startpage.js', '/app/common.js']);

$app->backle->writePageHeader();

?>

    <br/>
    <div class="row" ng-controller="IndexCtrl">
      <div class="col-md-7 col-md-offset-1" id="item-list">
        
        <ul style="padding-left: 0px;" ng-repeat="project in projects | filter:searchText ">
          <li class="list-group-item"><i class="glyphicon glyphicon-chevron-right"></i> <a href="<?=cfg_basepath()?>/{{project.name}}">{{project.title}} (/{{project.name}})</a></li>
        </ul>
      </div>
      <div class="well" style="width: 300px; margin: 0px 0px 20px 30px; padding: 5px; float:left;">
        You can create your own backlog in a few seconds.
        
        <a href="<?=cfg_basepath()?>/c/create"><span class="glyphicon glyphicon-arrow-right"></span> create a project</a>
        <br/>

        <h3>search for backlogs:</h3>
            <div class="input-group pull-right">
              <input type="text" class="form-control" placeholder="Search" ng-model="searchText"/>
              <div class="input-group-btn">
                <button class="btn btn-default" ng-click="searchText = ''" ng-disabled="! searchText" type="submit"><i class="glyphicon glyphicon-remove"></i></button>
              </div>
            </div>
      </div>
    </div>

<?php
$app->backle->writePageFooter();
?>