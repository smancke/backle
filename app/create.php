<?php

$app->backle->writeHead('the agile backlog',
                              ['/app/create.js', '/app/common.js']);

$app->backle->writePageHeader();

?>

<br>
<div ng-controller="CreateCtrl">
    <div class="well" style="width: 400px; margin: auto">
      <h3 ng-show="!projectname">Create Project</h3>
      <h3 ng-show="projectname">Create Backlog</h3>
      <div class="{{alertType}}" ng-bind-html="alertHtmlMessage"></div>
      <form role="form">

        <div class="form-group" ng-show="projectname">
          <label for="name">Project: {{projectname}}</label>          
        </div>
        <div class="form-group">
          <label for="name">Name</label>          
          <input id="name" type="text" placeholder="e.g. my_backlog" class="form-control" ng-model="name">
        </div>
        <div class="form-group">
          <label for="title">Title</label>          
          <input id="title" type="text" placeholder="e.g. My cool Backlog" class="form-control" ng-model="title">
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
</div>
  </body>
</html>
