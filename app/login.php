<!DOCTYPE html>
<html ng-app="backle">
  <head>
    <title>backle - login</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php include('headSection.php') ?>
    <script src="<?=cfg_basepath()?>/app/login.js"></script>
    <script src="<?=cfg_basepath()?>/app/common.js"></script>
  </head>
  <body>
<?php include('header.php') ?>

   <?php

if ($errorMessage) {
      echo '<div class="alert alert-danger">'.$errorMessage.'</div>';
}

   ?>
    <br>
    <div class="thumbnail center well text-center">
      <br>
      <br>
      <a href="<?=cfg_basepath()?>/c/loginRedirect"><img src="<?=cfg_basepath()?>/app/images/google-sign-in.png"></a>
      <br>
      <br>
    </div>

  </body>
</html>
