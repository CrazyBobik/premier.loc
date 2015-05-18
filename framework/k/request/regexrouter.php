<?php 

/**
 * RegexRouter router
 * Set the pattern string on create
 * Regex string must contain named attributes (?P<attribute-name>...) as:
 * 	module => "default"
 *  controller => "index"
 *  action => "index"
 *  params as "a/b/c/d" => a=b, c=d
 */

class K_Request_Regexrouter implements K_Request_Irouter {
	protected $regex = '';
	
	public function __construct( $regex = '' ) {
		$this->regex = $regex;
	}
	
	public function assemble( $url = null ) {
		if ( empty($url) ) {
			$url = $_SERVER['REQUEST_URI'];
		}
		
		$matches = array();
		if ( preg_match( $this->regex, $url, $matches ) ) {
			
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