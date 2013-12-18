<?php

$app->backle->writeHead('the agile backlog',
                              ['/app/login.js', '/app/common.js']);

$app->backle->writePageHeader();

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
