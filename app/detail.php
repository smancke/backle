<!DOCTYPE html>
<html ng-app="backle" ng-controller="DetailCtrl">
  <head>
    <title>backle - {{story.title}}</title>

    <?php include('headSection.php') ?>

    <script src="<?=cfg_basepath()?>/app/lib/ckeditor/ckeditor.js"></script>    
    <script src="<?=cfg_basepath()?>/app/detail.js"></script>    
    <script src="<?=cfg_basepath()?>/app/common.js"></script>
  </head>
  <body>
<?php include('header.php') ?>

     <br>
     <div class="container">
       <div class="panel panel-success">
         <div class="panel-heading">
           <h3 id="headline"><div style="display: inline-block; min-height: 28px; min-width: 100px; margin: 0px; padding: 0px; padding-right: 10px"
                                  contentEditable="{{permissions.write}}"
                                  ng-model="story.title"></div></h3>

           <div style="display: inline-block; min-height: 18px; min-width: 100px; margin: 0px; padding: 0px; padding-right: 6px"
                contentEditable="{{permissions.write}}"
                ng-model="story.text"></div>
          </div>
           <div class="panel-body">
            <div class="row">       
            <div class="col-md-8" ckedit="story.detail" style="min-height:300px;"></div>
              <div class="col-md-4">
                <br/>
                <div class="row">
                  <!-- <div class="col-xs-4"><strong>Sprint</strong></div> <div class="col-xs-3">3</div> -->
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>StoryPoints</strong></div>                   
                  <div class="col-xs-3" style="display: inline-block; min-height: 18px; min-width: 100px; margin: 0px; padding: 0px; padding-right: 6px"
                       contentEditable="{{permissions.write}}"
                       ng-model="story.points"></div>
                </div>
                <div class="row">
                  <!--<div class="col-xs-4"><strong>Author</strong></div> <div class="col-xs-3">smancke</div>-->
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4"><strong>Erstellt</strong></div> <div class="col-xs-8">{{story.created}}</div>                
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Aktualisiert</strong></div> <div class="col-xs-8">{{story.changed}}</div>
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Erledigt</strong></div> <div class="col-xs-8">{{story.done}}</div>
                </div>

              </div>
            </div>
          </div>
       </div>
     </div>
     
  </body>
</html>
