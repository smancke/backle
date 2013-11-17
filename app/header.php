<div class="navbar-inverse" ng-controller="HeaderCtrl">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a tabindex="-1" ng-show="backlogPresent" class="navbar-brand" href="<?=cfg_basepath()?>/{{backlogname}}">Backlog: {{backlogname}}</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
      </ul>

      <div class="navbar-right">
        <ul class="nav navbar-nav">
          <li><a tabindex="-1" href="<?=cfg_basepath()?>/">backlogs</a></li>
<?php if ($app->userInfo): ?>
          <li><a tabindex="-1" href="<?=cfg_basepath()?>/c/create">create backlog</a></li>
        <li class="dropdown">
         <a tabindex="-1" style="padding: 0px;" class="dropdown-toggle" data-toggle="dropdown" href="#">
<?php if ($app->userInfo['image_url']): ?>
  <img style="padding:0px; margin:0px; height:40px; width:40px;" src="<?=$app->userInfo['image_url']?>" title="<?=$app->userInfo['displayname']?>">
<?php else: ?>
  <span style="padding:10px;" class="glyphicon glyphicon-user" title="<?=$app->userInfo['displayname']?>"></span>
<?php endif ?>
         <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a tabindex="-1" href="<?=cfg_basepath()?>/c/logout"><span class="glyphicon glyphicon-ban-circle"></span> Logout</a></li>
            </ul>
        </li>
<?php endif ?>
       </ul>
<?php if (!$app->userInfo): ?>
        <a tabindex="-1" href="<?=cfg_basepath()?>/c/loginRedirect"><img style="margin-top: 4px;" src="<?=cfg_basepath()?>/app/images/google-sign-in.png"/></a>
<?php endif ?>
     </div>
    </div>
</div>
