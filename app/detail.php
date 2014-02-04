<?php

$app->backle->writeHead('the agile backlog',
                        ['/app/lib/ckeditor/ckeditor.js', '/app/detail.js', '/app/common.js']);

$app->backle->writePageHeader();

?>

  <div ng-controller="DetailCtrl">
      <br>
    <div class="container">
      <div class="">
        <a href="<?=cfg_basepath()?>/<?=$projectname?>"
           class="btn btn-default btn-sm">
          <span class="glyphicon glyphicon-chevron-left"></span> liste
        </a>
      </div>
      <br>
        <div class="panel panel-default">
         <div class="panel-heading">
           <h3 id="headline"><div  style="display: inline-block; min-height: 28px; min-width: 100px; margin: 0px; padding: 0px; padding-right: 10px"
                                   contentEditable="{{permissions.write == 1}}"
                                   ng-model="story.title"
                                   maxlength="400"></div></h3>

           <div style="display: inline-block; min-height: 18px; min-width: 100px; margin: 0px; padding: 0px; padding-right: 6px"
                contentEditable="{{permissions.write == 1}}"
                ng-model="story.text"
                maxlength="800"></div>
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
                  <div class="col-xs-3" style="display: inline-block; min-height: 18px; min-width: 100px; padding-right: 6px"
                       contentEditable="{{permissions.write == 1}}"
                       ng-model="story.points"
                       maxlength="3"
                       numberonly></div>
                </div>
                <div class="row">
                  <!--<div class="col-xs-4"><strong>Author</strong></div> <div class="col-xs-3">smancke</div>-->
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4"><strong>Erstellt</strong></div> <div class="col-xs-8">{{story.created | dbDataToJs | date:'medium'}}</div>
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Aktualisiert</strong></div> <div class="col-xs-8">{{story.changed | dbDataToJs | date:'medium'}}</div>
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Erledigt</strong></div> <div class="col-xs-8">{{story.done | dbDataToJs | date:'medium'}}</div>
                </div>

              </div>
            </div>
          </div>
       </div>
     </div>
  </div>
     
  </body>
</html>
