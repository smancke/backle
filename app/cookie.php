<?php

$app->setCookie('bli', 'bla', time() - 12*3600);

$app->redirect('/');

?>