<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>Create Backlog</title>

    <?php include('headSection.php') ?>
    
    <script src="<?=cfg_basepath()?>/app/create.js"></script>
    <script src="<?=cfg_basepath()?>/app/common.js"></script>
  </head>
  <body ng-controller="CreateCtrl">
<?php include('header.php') ?>

    <br>
    <div class="well" style="width: 400px; margin: auto">
      <h3>Create Backlog</h3>
      <div class="{{alertType}}" ng-bind-html="alertHtmlMessage"></div>
      <form role="form">

        <div class="form-group" ng-show="projectname">
          <label for="name">Project: {{projectname}}</label>          
        </div>
        <div class="form-group">
          <label for="name">Name</label>          
          <input id="name" type="text" placeholder="e.g. my_backlog" class="form-control" style="width: 300px;" ng-model="backlogname">
        </div>
        <div class="form-group">
          <label for="title">Title</label>          
          <input id="title" type="text" placeholder="e.g. My cool Backlog" class="form-control" style="width: 300px;" ng-model="backlogtitle">
        </div>
        <div class="checkbox">
          <label>
           <input type="checkbox" name="is_public_viewable" value="1" ng-model="is_public_viewable"> Public viewable
          </label>
        </div>
        <br />
        <input type="submit" value="Create Now!" class="btn btn-large" ng-click="create()"/>
      </form>
    </div>
  </body>
</html>
