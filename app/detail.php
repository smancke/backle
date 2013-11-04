<!DOCTYPE html>
<html ng-app="backle" ng-controller="DetailCtrl">
  <head>
    <title>backle - {{story.title}}</title>

    <?php include('headSection.php') ?>

    <script src="<?=cfg_basepath()?>/app/lib/xeditable.min.js"></script>    
    <link rel="stylesheet" href="<?=cfg_basepath()?>/app/lib/xeditable.css" type="text/css"/>

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
           <h3 id="headline"><a href="#" editable-text="story.title" blur="submit" e-style="display: inline; width: 600px;">{{story.title || 'edit ..'}}</a></h3>
           <a href="#" editable-text="story.text" blur="submit" e-style="width: 600px;">{{story.text || 'edit ..'}}</a>
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
                <div class="col-xs-4"><strong>StoryPoints</strong></div> <a href="#" class="col-xs-3" editable-text="story.points" blur="submit" e-style="width: 60px">{{story.points || 'edit ..'}}</a>
                </div>
                <div class="row">
                  <!--<div class="col-xs-4"><strong>Author</strong></div> <div class="col-xs-3">smancke</div>-->
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4"><strong>Erstellt</strong></div> <div class="col-xs-8">{{story.created}}</div>                
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Aktualisiert</strong></div> <div class="col-xs-8">{{story.updated}}</div>
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
