<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>Backlog</title>

    <?php include('headSection.php') ?>
    
    <script src="<?=cfg_basepath()?>/app/list.js"></script>
    <script src="<?=cfg_basepath()?>/app/common.js"></script>
  </head>
  <body>
    
    <?php include('header.php') ?>

    <!-- content -->
    <br>
    <div ng-controller="ListCtrl">

      <div class="{{alertType}}"  ng-bind-html="alertHtmlMessage"></div>

      <div ng-show="backlogPresent">
        <div class="row">
          <div class="col-md-5 col-md-offset-1">
            <button ng-click="addItem()" type="button" class="btn  btn-default btn-lg">
              <span class="glyphicon glyphicon-plus"></span>
            </button>
            <button ng-click="addSprint()" type="button" class="btn  btn-default btn-lg">
              <span class="glyphicon glyphicon-plus"> Sprint</span>
            </button>
          </div>
          <div class="col-md-2">
            <span class="badge pull-right" style="min-width:32px; margin-right: 73px">{{totalStoryPoints}}</span>
          </div>  
        </div>
        <br>
        <div class="row">
          <div class="col-md-7 col-md-offset-1" id="item-list">

            <div id="item-{{backlogItem.id}}" 
                 class="backlog-list-item {{backlogItem.type}} {{backlogItem.status}}" 
                 ng-repeat="backlogItem in backlogItems" 
                 ng-click="focus($event)">

              <a class="detail-link" href="<?=cfg_basepath()?>/{{backlogname}}/{{backlogItem.id}}">#{{backlogItem.id}}</a> 
              <span class="milestone-block">
                <span id="item-title-{{backlogItem.id}}"
                      class="backlog-item-title"
                      contentEditable="true"
                      ng-model="backlogItem.title" 
                      ng-keypress="itemTitleKeyPressed($event)"></span>
                
                <div class="backlog-item-right">
                  <span class="badge" style="min-width:32px;" ng-click="focus($event)"><span class="badge-text" contenteditable="true" ng-model="backlogItem.points"></span></span>
                  <div class="backlog-item-buttons">
                    <a class="backlog-btn" href="#" ng-click="markAsDone(backlogItem)" title="done/open" tabindex="-1">
                      <div class="glyphicon glyphicon-ok"></div></a>
                    <a class="backlog-btn" href="#" ng-click="deleteItem(backlogItem)" title="delete" tabindex="-1">
                      <div class="glyphicon glyphicon-trash"></div></a>
                  </div>
                </div>
              </span>

            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
