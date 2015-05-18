<?php


    defined('ROOT_PATH')
		|| define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));   
 
	require_once  realpath(dirname(__FILE__) . '/all_config.php');
 
	
	// Ensure library/ is on include_path
	set_include_path(implode(PATH_SEPARATOR, array(
		realpath(ROOT_PATH),
		realpath(K_PATH),
		realpath(ROOT_PATH . '/library'),
		get_include_path(),
	)));

	/** Zend_Application */
	require_once K_PATH.'/application2.php';

	// Create application, bootstrap, and run
	$application = new K_Application(
		ROOT_PATH,
		ROOT_PATH . '/configs/system/application.ini'
	);

	$application->addHeader( 'Content-type: text/html; charset=utf-8' );

	try {
	
		$application->bootstrap()->run();
		
	} catch ( Exception $e ){
	
		echo 'Exception: '.$e->getMessage();
		
	}
  
?>