<?php

require_once "/etc/backle/config-db.php";

$cfg['basepath'] = '/backle';

$cfg['dbtype'] = 'postgresql';
$cfg['dbhost'] = ($dbserver) ? $dbserver : 'localhost';
$cfg['dbuser'] = $dbuser;
$cfg['dbpassword'] = $dbpass;
$cfg['dbname'] = $dbname;
$cfg['dbschema'] = $basepath; // relevant for postgres, only
$cfg['dblogfile'] = Null;

$cfg['demo_login_enabled'] = false;
$cfg['demo_login_password'] = 'secret';

$cfg['google']['client_id'] = 'your-client-id.apps.googleusercontent.com';
$cfg['google']['client_secret'] = 'your-client-secret-hlkmlzztmnksdcsd';
$cfg['google']['redirect_uri'] = 'http://127.0.0.1/app/c/login';

$cfg['embedded_in_gforge'] = true;

function cfg_basepath() {
    global $cfg;
    return $cfg['basepath'];
}

?>
