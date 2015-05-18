<?php 

/**
 * Class Request
 * <example>
 *  if ( !K_Request::isAjax() ) {
 *		K_Request::redirect('http://yandex.ru');
 *	}
 * </example>
 */

class K_Request {
	protected static $rewriteList = array();
	protected static $params;
	protected static $_isPost = false;
	protected static $_isAjax = false;
		
	protected function __construct() {		
		
	}
	
	public static function init() {
		self::$_isPost = is_array($_POST) && count($_POST);
		self::$_isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) == 'xmlhttprequest';
	}
	
	public static function isPost() {		
		return self::$_isPost;
	}
	
	public static function get( $paramName ) {
		if ( isset(self::$params[ $paramName ]) ) {
			return self::$params[ $paramName ];
		}
		return null;
	}
	
	public static function route() {
		if ( !count(self::$rewriteList) ) {
			self::$rewriteList[] = new K_Request_Router();
		}
		
		$result;
		foreach( self::$rewriteList as $router ) {
			if ( $result = $router->assemble() ) {				
				self::$params = isset($result['params'])?$result['params']:null;
				$result['params'] = &self::$params;
				return $result;
			}
		}
		return null;
	}
	
	public static function addRewriteRule( $rule ) {
		if ( $rule instanceof K_Request_IRouter ) {
			self::$rewriteList[] = $rule;
		} elseif ( is_string($rule) ) {
			self::$rewriteList[] = new K_Request_RegexRouter( $rule );
		}
	}
	
	public static function redirect( $url, $code = null ) {
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
	
	public static function isAjax() {
		return self::$_isAjax;
	}
    
	public static function call( $routerInfo = array(), $cacheArray = null, $fastDrawCacheData = false  ) {
		// load from cache
		if ( isset($cacheArray) && !empty($cacheArray) && is_array($cacheArray) ) {				
				if ( $cacheArray['manager']->test( $cacheArray['id'] ) ) {					
					return $cacheArray['manager']->loadRender( $cacheArray['id'], $fastDrawCacheData );
				}
		}
		
		$routerInfo['disableLayout'] = isset($routerInfo['disableLayout'])?$routerInfo['disableLayout']:true;
		$routerInfo['breakOnRender'] = false;
		$routerInfo['caller'] = 1;  // 0 - call from internet request, 1 - call local

		K_Capture::start();
		K_Application::get()->executeRequest( $routerInfo, true, false); // добавил отключение ACl при HMVC запросах
		$html = K_Capture::end();
		
		// save to cache
		if ( isset($cacheArray) && !empty($cacheArray) && is_array($cacheArray) ) {
				$cacheArray['manager']->saveRender( 
					$cacheArray['id'], 
					$html,  
					isset($cacheArray['tags'])&&is_array($cacheArray['tags'])?$cacheArray['tags']:array(),  
					isset($cacheArray['lifetime'])?(int)$cacheArray['lifetime']:0
				);
		}
		
		return $html;
	}    
    
}

?>