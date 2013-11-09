<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>backle - the agile backlog</title>

    <?php include('headSection.php') ?>

    <script src="<?=cfg_basepath()?>/app/startpage.js"></script>
    <script src="<?=cfg_basepath()?>/app/common.js"></script>
  </head>
  <body ng-controller="IndexCtrl">
<?php include('header.php') ?>

    <br>
    <div class="row">
      <div class="col-md-7 col-md-offset-1" id="item-list">
        
        <ul ng-repeat="backlog in backlogs | filter:searchText ">
          <li class="list-group-item"><i class="glyphicon glyphicon-chevron-right"></i> <a href="<?=cfg_basepath()?>/{{backlog.backlogname}}">{{backlog.backlogtitle}} (/{{backlog.backlogname}})</a></li>
        </ul>
      </div>
      <div class="well" style="width: 300px; margin: 0px 0px 20px 30px; padding: 5px; float:left;">
        You can create your own backlog in a few seconds.
        
        <a href="<?=cfg_basepath()?>/c/create"><span class="glyphicon glyphicon-arrow-right"></span> create a backlog</a>
<!--
        <br>
        <br>
        If you have to manage a group of people or multiple backlogs,
        you may collect them within a project.
        <br>
        <a href="<?=cfg_basepath()?>/c/create"><span class="glyphicon glyphicon-arrow-right"></span> create a project</a>
-->
        <br>

        <h3>search for backlogs:</h3>
            <div class="input-group pull-right">
              <input type="text" class="form-control" placeholder="Search" ng-model="searchText">
              <div class="input-group-btn">
                <button class="btn btn-default" ng-click="searchText = ''" ng-disabled="! searchText" type="submit"><i class="glyphicon glyphicon-remove"></i></button>
              </div>
            </div>
      </div>

  </body>
</html>
