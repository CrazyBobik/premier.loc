<?php 

/**
 * Default router
 * /module/controller/action/param1/value1/...
 */

class K_Request_Router implements K_Request_Irouter {
	public function assemble( $url = null ) {
		if ( empty($url) ) {
			$url = $_SERVER['REQUEST_URI'];
		}
		
		$matches = array();
		if ( preg_match( '/^\/(?P<module>[a-z0-9_-]+)?(\/(?P<controller>[a-z0-9_-]+)(\/(?P<action>[a-z0-9_-]+)(\/(?P<params>.*)?)?)?)?/is', $url, $matches ) ) {
			
			$params = array();
			
			if ( !empty($matches['params']) ) {
				$p = explode( '/', $matches['params'] );
				if ( count($p)) {
					for ($i=0; $i<count($p); $i+=2) {
						$params[ $p[$i] ] = isset( $p[$i+1] )?$p[$i+1]:null;
					}
				}
			}
		
			$result = array(
				'module' => isset($matches['module'])?$matches['module']:'default',
				'controller' => isset($matches['controller'])?$matches['controller']:'index',
				'action' =>	isset($matches['action'])?$matches['action']:'index',
				'params' =>	$params
			);
						
			return $result;
		}
		return null;
	}
}

?>