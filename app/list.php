<?php

$app->backle->writeHead('the agile backlog',
                              ['/app/list.js', '/app/common.js']);

$app->backle->writePageHeader();

?>

    <!-- content -->
    <br/>
    <div ng-controller="ListCtrl">

      <div class="{{alertType}}"  ng-bind-html="alertHtmlMessage"></div>
     
      <div ng-show="backlogPresent">
        <div class="row">
          <div class="col-md-5 col-md-offset-1">
            <button ng-show="permissions.write" ng-click="addItem()" type="button" class="btn  btn-default btn-lg">
              <span class="glyphicon glyphicon-plus"></span> Story
            </button>
            <button ng-show="permissions.write" ng-click="addSprint()" type="button" class="btn  btn-default btn-lg">
              <span class="glyphicon glyphicon-plus"></span> Sprint
            </button>
          </div>
          <div class="col-md-2">
            <div class="input-group pull-right">
              <input type="text" class="form-control" placeholder="Search" ng-model="searchText"/>
              <div class="input-group-btn">
                <button class="btn btn-default" ng-click="searchText = ''" ng-disabled="! searchText" type="submit"><i class="glyphicon glyphicon-remove"></i></button>
              </div>
            </div>
            <!-- <span class="badge pull-right" style="min-height:18px; min-width:36px; margin-right: 40px">{{totalStoryPoints}}</span> -->
          </div>  
        </div>
        <br/>
        <div class="row">
          <div class="col-md-7 col-md-offset-1" id="item-list">

            <div id="item-{{backlogItem.id}}" 
                 class="backlog-list-item {{backlogItem.type}}" 
                 ng-repeat="backlogItem in backlogItems | filter: searchText" 
                 ng-click="focus($event)">
              
              <div class="detail-link"><a href="<?=cfg_basepath()?>/<?=$projectname?>/{{backlogItem.id}}">#{{backlogItem.id}}</a></div>
              <span class="milestone-block">
                <div style="display: inline-block; min-height: 18px; min-width: 3px; margin: 0px; padding: 0px;" id="item-title-{{backlogItem.id}}"
                      class="backlog-item-title"
                      contentEditable="{{permissions.write == 1}}"
                      ng-model="backlogItem.title" 
                      ng-keypress="itemTitleKeyPressed($event)"></div>
                
                <a class="backlog-btn" href="#" style="cursor: default;" ng-show="permissions.write || backlogItem.status == 'done'" ng-click="permissions.write && markAsDone(backlogItem)" title="done/open" tabindex="-1" style="padding: 5px">
                  <div class="glyphicon glyphicon-ok" ng-class="{'greenOk': backlogItem.status == 'done', 'greyOk': backlogItem.status != 'done'}"></div></a>

                
                <div class="backlog-item-right">
                  <span class="badge" style="min-width:36px; min-height:18px; margin-right: 3px;" ng-click="focus($event)">
                    <span tabindex="-1" class="badge-text" 
                          style="padding: 1px;" 
                          contenteditable="{{permissions.write == 1}}" 
                          ng-model="backlogItem.points"
                          placeholder="  "></span>
                  </span>
                  <div class="backlog-item-buttons" ng-show="permissions.write">
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
     <br/>

<?php
$app->backle->writePageFooter();
?>