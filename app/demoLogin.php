<?php

$app->backle->writeHead('the agile backlog', []);

$app->backle->writePageHeader();

?>

    <br>
    <div class="well" style="width: 400px; margin: auto">
      <h3>Demo Login</h3>
<?php
if ($errorMessage) {
      echo '<div class="alert alert-danger">'.$errorMessage.'</div>';
}
?>

      <form action="" method="POST" role="form">
        <div class="form-group">
          <label for="name">Passwort</label>          
          <input name="demo_login_password" type="text" class="form-control" style="width: 300px;">
        </div>
        <br />
        <input type="submit" value="Login" class="btn btn-default">
      </form>
    </div>

  </body>
</html>
