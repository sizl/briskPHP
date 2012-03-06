<?php

define('DEV', 'dev');
define('PROD', 'prod');

define('ENV_MODE', $_SERVER['APP_MODE']);
define('ROOT_PATH', $_SERVER['ROOT_PATH']);
define('MODULE', $_SERVER['MODULE']);
define('MODULE_PATH', ROOT_PATH . 'modules');

function __autoload($class){
	$class = str_replace('_', '/', $class);
	$class.= '.php';
	require_once("$class"); 
}

set_include_path(
	get_include_path() . PATH_SEPARATOR .
	ROOT_PATH . '/library' . PATH_SEPARATOR .
	MODULE_PATH
);

error_reporting(E_ALL);
ini_set('display_errors', (APP_MODE == DEV) ? 0: 1);
	
