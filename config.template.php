<?php

$cfg['basepath'] = '/backle';

$cfg['dbhost'] = 'localhost';
$cfg['dbuser'] = 'backle';
$cfg['dbpassword'] = 'backle';
$cfg['dbname'] = 'backle';

$cfg['demo_login_enabled'] = true;
$cfg['demo_login_password'] = 'secret';

$cfg['cookie_secret'] = 'secret_key_for_cookies';

$cfg['google']['client_id'] = 'your-client-id.apps.googleusercontent.com';
$cfg['google']['client_secret'] = 'your-client-secret-hlkmlzztmnksdcsd';
$cfg['google']['redirect_uri'] = 'http://127.0.0.1/app/c/login';

$cfg['embedded_in_gforge'] = false;

function cfg_basepath() {
    global $cfg;
    return $cfg['basepath'];
}

?>
