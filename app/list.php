<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>Backlog</title>

    <? include('headSection.php') ?>
    
    <script src="./common.js"></script>
    <script src="./list.js"></script>
  </head>
  <body>
    
    <? include('header.php') ?>
    
    <!-- content -->
    <br>
    <div ng-controller="ListCtrl">

      <div class="{{alertType}}"  ng-bind-html="alertHtmlMessage"></div>

      <div ng-show="backlogPresent">
        <div class="row">
          <div class="col-md-1 col-md-offset-1">
            <button ng-click="addItem()" type="button" class="btn  btn-default btn-lg">
              <span class="glyphicon glyphicon-plus"></span>
            </button>
          </div>
        </div>
        <br>
        <div class="row">
        <div class="col-md-7 col-md-offset-1" id="item-list">
          <div id="item-{{backlogItem.id}}" class="backlog-list-item {{backlogItem.status}}" ng-repeat="backlogItem in backlogItems" ng-click="focus($event)">
            <a href="detail.php?backlogname={{backlogname}}">#{{backlogItem.id}}</a> 
            <span class="backlog-item-title" id="item-title-{{backlogItem.id}}" contentEditable="true" ng-model="backlogItem.title" ng-keypress="itemTitleKeyPressed($event)"></span>
            <div class="backlog-item-buttons">
              <a class="backlog-btn" href="#" ng-click="markAsDone(backlogItem)" title="done/open" tabindex="-1">
                <div class="glyphicon glyphicon-ok"></div></a>
              <a class="backlog-btn" href="#" ng-click="deleteItem(backlogItem)" title="delete" tabindex="-1">
                <div class="glyphicon glyphicon-trash"></div></a>
            </div>
          </div>
        </div>     
        </div>
      </div>
    </div>
  </body>
</html>
