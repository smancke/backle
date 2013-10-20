<div class="navbar-inverse" ng-controller="HeaderCtrl">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a ng-show="backlogPresent" class="navbar-brand" href="<?=cfg_basepath()?>/{{backlogname}}">Backlog: {{backlogname}}</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
      </ul>

<!--
      <div class="navbar-form navbar-right" role="search">
        <input ng-show="backlogPresent" type="text" name="search" class="form-control" id="search" placeholder="search .."/>
      </div>
-->
      <div class="navbar-right">
        <ul class="nav navbar-nav">
          <li><a class="dropdown-toggle" data-toggle="dropdown" href="<?=cfg_basepath()?>/">backlogs</a></li>
          <li><a class="dropdown-toggle" data-toggle="dropdown" href="<?=cfg_basepath()?>/app/create.php">create backlog</a></li>
        </ul>
     </div>
    </div>
</div>
