<?php

error_reporting(0);
ini_set("magic_quotes_runtime", 0);

define('IN_VWS', TRUE);
define('VWS_ROOT', substr(dirname(__FILE__), 0, -3));

function __autoload($className){
    if(preg_match('#Core$#', $className) && file_exists($classPath = VWS_ROOT."/core/{$className}.php")){
        include_once $classPath; 
    }
}

include_once VWS_ROOT . '/core/DBCore.php';
include_once VWS_ROOT . '/core/DictCore.php';
include_once 'config.inc.php';

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpassword, $dbname);

?>
