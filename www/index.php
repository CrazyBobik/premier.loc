<?php

if($_SERVER['REMOTE_ADDR']!='195.138.90.140'){
    exit();
}
// костыл с редиректами, в хтацесс не хотели работать редиректы

function my_redirect( $url, $code = null ) {

    if ( !empty($code) ) {
        switch ((int)$code) {
            case 301: header("HTTP/1.0 301 Moved Permanently"); break;
            case 404: header("HTTP/1.0 404 Not Found"); break;
            default: header("HTTP/1.0 200 OK"); break;
        }
    }

    header('Location: '.$url);
    die();
}

if(isset($_GET['page']) && $_GET['page']<=1){

    my_redirect(preg_replace('/[\?\&]page=\d*/si', '', $_SERVER['REQUEST_URI']), '301');

}

session_start();
//var_dump( $HTTP_RAW_POST_DATA);

//$request_body = file_get_contents('php://input');
//var_dump($request_body);

error_reporting ( E_ALL);

defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

require_once '../configs/all_config.php';

// время жизни сессии 24 часа. 
//ini_set('session.gc_maxlifetime', 60*60*24);
//ini_set('session.cookie_lifetime', 60*60*24);
//отдельная папка для хранения сессий

//ini_set('session.save_path', ROOT_PATH.'/cache/sessions');

//session_start();

if (extension_loaded('xhprof')){
        
    // профайлер 
    include_once ROOT_PATH.'/spot/classes/xhprof_lib.php';
    include_once ROOT_PATH.'/spot/classes/xhprof_runs.php';
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
 
} 

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
												realpath(ROOT_PATH),
												realpath(K_PATH),
												realpath(ROOT_PATH . '/library'),
												get_include_path(),
											   )));

/** Zend_Application */
require_once K_PATH.'/application.php';

// Create application, bootstrap, and run
$application = new K_Application(
    ROOT_PATH,
    ROOT_PATH . '/configs/system/application.ini'
);

$application->addHeader( 'Content-type: text/html; charset=utf-8' );

try {
	$application->bootstrap()->run();		
} catch ( Exception $e ) {
	echo 'Exception: '.$e->getMessage();
}

$application->dispatch();

?>