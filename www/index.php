<?php
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

$qpos = strpos($_SERVER['REQUEST_URI'], '?');

if ($qpos) {

$url = $expuri  = substr( $_SERVER['REQUEST_URI'], 0, strpos( $_SERVER['REQUEST_URI'], '?'));
	
}else{
	
$url = $expuri  = $_SERVER['REQUEST_URI'];

};		


$redirects = array(

	'/iskra.htm'=>'/iskra-sloveniya',
	'/vch_r.htm'=>'/iskra-sloveniya',
	'/et8_r.htm'=>'/iskra-sloveniya/sparkenergy--czifrovoe-oborudovanie-vch-svyazi-i-rz/apparatura-vch-svyazi',
	'/nf8_r.htm'=>'/iskra-sloveniya/sparkenergy--czifrovoe-oborudovanie-vch-svyazi-i-rz/nch-terminal-dostupa',
	'/dz8_r.htm'=>'/iskra-sloveniya/sparkenergy--czifrovoe-oborudovanie-vch-svyazi-i-rz/czifrovaya-apparatura-peredachi-komand-rz',
	'/esv6_r.htm'=>'/iskra-sloveniya/oborudovanie-prisoedineniya-k-vl/filtr-prisoedineniya-esv6',
	'/esc8_r.htm'=>'/',
	'/aster5_r.htm'=>'/iskra-sloveniya/sparkenergy--czifrovoe-oborudovanie-vch-svyazi-i-rz/vneshnij-modem-dlya-peredachi-dannyix',
	'/vfz6_r.htm'=>'/iskra-sloveniya/oborudovanie-prisoedineniya-k-vl/razdelitelnyj-filtr-vfz6-1',
	'/pdf/dz9.pdf'=>'/iskra-sloveniya/sparkenergy--czifrovoe-oborudovanie-vch-svyazi-i-rz/czifrovaya-apparatura-peredachi-komand-rz',
	'/transmission/sparkwave.htm'=>'/iskra-sloveniya',
	'/transmission/sparkwave_7_8.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/radiorelejnaya-sistema-srednei-jomkosti',
	
	'/transmission/sparkwave_13.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/radiorelejnaya-sistema-srednei-jomkosti',
	'/transmission/sparkwave_15_18_23.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/radiorelejnaya-sistema-srednei-jomkosti',
	'/transmission/sparkwave_18_23ar.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/aktivniy-retranslyator',
	'/transmission/eym34_a1.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/apparatura-peredachi-ezernet-po-rrl',
	'/transmission/ewm34_a1.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/apparatura-peredachi-gigabit-ezernet-po-rrl',
	'/transmission/ewm34_a2.htm'=>'/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/apparatura-peredachi-gigabit-ezernet-po-rrl',
	'/sparklight_adm.htm'=>'/iskra-sloveniya/sparklight--volokon-opticheskie-sistemyi-peredachi/vyisokoskorosnoi-pdh-multipleksor',
	'/transmission/eu8.htm'=>'/iskra-sloveniya/sparklight--volokon-opticheskie-sistemyi-peredachi/vyisokoskorosnoi-pdh-multipleksor',
	'/transmission/vu2.htm'=>'/iskra-sloveniya/sparklight--volokon-opticheskie-sistemyi-peredachi/vyisokoskorosnoi-pdh-multipleksor',
	'/transmission/sparklight_wdm.htm'=>'/iskra-sloveniya/sparklight--volokon-opticheskie-sistemyi-peredachi/mnogofunctionalniy-sdh-multipleksor',
	'/transmission/sparkline.htm'=>'/iskra-sloveniya/sparklight--volokon-opticheskie-sistemyi-peredachi/sdh-multiplexor-vvoda-vivoda',
	'/transmission/sparkview.htm'=>'/iskra-sloveniya/sparkview--sistema-upravleniya-elementami-seti/apparatura-peredachi-komand-rz-i-pa',
	'/cap_r.htm'=>'/drugaya-produkcziya',
	'/cs_r.htm'=>'/drugaya-produkcziya/kondensatoryi-svyazi',
	'/zv_r.htm'=>'/sobstvennoe-proizvodstvo/vyisokochastotnyie-zagraditeli-vz',
	'/fp.htm'=>'/sobstvennoe-proizvodstvo/filtryi-prisoedineniya-fp',
	'/shonk_r.htm'=>'/drugaya-produkcziya/shkafyi-otbora-napryazheniya',
	'/shonp_r.htm'=>'/drugaya-produkcziya/shkafyi-otbora-napryazheniya',
	'/shon_r.htm'=>'/drugaya-produkcziya/shkafyi-otbora-napryazheniya',
	'/measuring.htm'=>'/',
	'/pdf/et_70da.pdf'=>'/',
	'/m8_r.htm'=>'/',
	'/adress.html'=>'/contacts',

	'/iskra.htm'=>'/iskra-sloveniya'

);


if(isset($redirects[$url])){
	
	my_redirect($redirects[$url], '301');

};


$expuri = explode('/', 	$expuri);

 
if( $expuri['1'] == 'transfer'){

    require_once('../cli/transfer.php');
    exit();
	
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