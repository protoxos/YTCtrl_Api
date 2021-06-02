<?php
	define('API_VERSION', '1.0');

	//	Headers
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
		die();
	}

	
	// Database
	define('DB_NAME', 'protoxos_ytctrl');
	define('DB_USER', 'protoxos_db');
	define('DB_PASS', ']g+coA$1pC!!');


	//	Nucleo
	require_once "core/enums.php";
	require_once "core/db.php";
	require_once "core/functions.php";
	
	
	$action = @$_GET['action'];
	
	if($action == 'register_action') register_action();
	elseif($action == 'register_info') register_info();
	elseif($action == 'get_actions') get_actions();
	elseif($action == 'get_info') get_info();

	elseif($action == 'version') service_end(Status::Success, API_VERSION);
	else service_end(Status::Success, 'I\' doing nothing... :3');