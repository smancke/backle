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

    <div class="row col-md-7 col-md-offset-1">
      <h1>Backlogs</h1>
    </div>
    <div class="row">
      <div class="col-md-7 col-md-offset-1" id="item-list">

        <ul ng-repeat="backlog in backlogs">
          <li class="list-group-item"><i class="glyphicon glyphicon-chevron-right"></i> <a href="<?=cfg_basepath()?>/{{backlog.backlogname}}">{{backlog.backlogname}}</a></li>
        </ul>
      </div>
  </body>
</html>
