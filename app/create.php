<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>Create Backlog</title>

    <? include('headSection.php') ?>
    
    <script src="./common.js"></script>
    <script src="./create.js"></script>
  </head>
  <body ng-controller="CreateCtrl">
<? include('header.php') ?>

    <br>
    <div class="thumbnail center well text-center">
      <h2>Create Backlog</h2>
      <div class="{{alertType}}" ng-bind-html="alertHtmlMessage"></div>
      <form action="" method="post">
        <br />
        <input id="name" type="text" placeholder="your backlogname" class="form-control" style="width: 300px; margin:auto;" ng-model="backlogname">
        <br />
        <br />
        <input type="submit" value="Create Now!" class="btn btn-large" ng-click="create()"/>
      </form>
    </div>
  </body>
</html>
