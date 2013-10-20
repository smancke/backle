<?php

$cfg['basepath'] = '/backle';

$cfg['dbhost'] = 'localhost';
$cfg['dbuser'] = 'backle';
$cfg['dbpassword'] = 'backle';
$cfg['dbname'] = 'backle';


function cfg_basepath() {
    global $cfg;
    return $cfg['basepath'];
}

?>
