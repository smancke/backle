<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>VerA.web - modify a user (#4711)</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="backle.css" type="text/css"/>
    <link href="lib/bootstrap.css" rel="stylesheet" media="screen">

    <script src="lib/angular.min.js"></script>
    <script src="lib/angular-resource.min.js"></script>
    <link rel="stylesheet" href="lib/xeditable.css" type="text/css"/>
    <script src="lib/xeditable.min.js"></script>
    <script src="lib/jquery.min.js"></script>
    <script src="lib/ui/jquery-ui.js"></script>
    <script src="lib/ckeditor/ckeditor.js"></script>

    <script>
     <?php 
     echo "    global_backlogname = '" . $_GET['backlogname'] ."'\n";
     echo "    global_storyid = '" . $_GET['storyid'] ."'\n";
     ?>
    </script>

    <script src="./detail.js"></script>    
    <script src="./common.js"></script>
  </head>
  <body>
<? include('header.php') ?>

     <br>
     <div class="container" ng-controller="DetailCtrl">
       <div class="panel panel-success">
         <div class="panel-heading">
           <h3 id="headline"><a href="#" editable-text="story.title">{{story.title || 'click to edit'}}</a></h3>
           <a href="#" editable-text="story.text">{{story.text || 'click to edit'}}</a>
          </div>
           <div class="panel-body">
            <div class="row">       
              <div class="col-md-8" ckedit="story.detail"></div>
              <div class="col-md-4">
                <br/>
                <div class="row">
                  <!-- <div class="col-xs-4"><strong>Sprint</strong></div> <div class="col-xs-3">3</div> -->
                </div>
                <div class="row">
                <div class="col-xs-4"><strong>StoryPoints</strong></div> <a href="#" class="col-xs-3" editable-select="story.points" buttons="no" e-ng-options="s.value as s.text for s in points">{{ showPoints() }}</a>
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
