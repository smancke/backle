<?php

global $cfg, $_GET;

require_once 'app/SimpleOAuthLogin/SimpleGoogleLogin.php';
$googleLogin = new SimpleGoogleLogin($cfg['google']);

$errorMessage = '';

if (isset($_GET['code'])) {
   if ($googleLogin->exchangeAuthCode($_GET['code'])) {
   
     $user = $googleLogin->getTokenInfo();
     error_log('user: '.json_encode($user));
     if (property_exists($user, 'email')) {
       $cookieData = ['email' => $user->email,
                      //$user->user_id,
                      'timestamp' => time()];
                   
       $app->setCookie('backle_auth', json_encode($cookieData), time() + 12*3600);
       $app->redirect(cfg_basepath() .'/');
    } else {
      // todo log login problems
      $errorMessage = '<h1>Error on google sign in.</h1>(Could not get user info)';
    }
  } else {
    // todo log login problems
    $errorMessage = '<h1>Error on google sign in.</h1>(Could not get access_token)';
  }
}

$login_uri = $googleLogin->getAuthUrl();

?><!DOCTYPE html>
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
      <a href="<?=$login_uri?>"><img src="<?=cfg_basepath()?>/app/images/google-sign-in.png"></a>
      <br>
      <br>
    </div>

  </body>
</html>
