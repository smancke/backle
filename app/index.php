<!DOCTYPE html>
<html ng-app="index">
  <head>
    <title>backle - the agile backlog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="backle.css" type="text/css"/>
    <link href="lib/bootstrap.css" rel="stylesheet" media="screen">

    <script src="lib/jquery.min.js"></script>
    <script src="lib/ui/jquery-ui.js"></script>
    <script src="lib/angular.min.js"></script>
    <script src="lib/angular-resource.min.js"></script>
    <script src="./index.js"></script>
  </head>
  <body ng-controller="IndexCtrl">

    <br/>
    <div class="row col-md-7 col-md-offset-1">
      <h1>Backlogs</h1>
    </div>
    <div class="row">
      <div class="col-md-7 col-md-offset-1" id="item-list">

        <ul class="list-group" ng-repeat="backlog in backlogs">
          <li class="list-group-item"><a href="list.php">{{backlog.backlogname}}</a></li>
        </ul>
      </div>
  </body>
</html>
