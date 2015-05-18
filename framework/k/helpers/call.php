<?php 

/**
 * Call Helper
 * <example> // in template
 	<?php $this->call->request( array(
		'module' => 'forum',
		'controller' => 'posts',
		'action' => 'get',
		'params' => array(
			'id' => 21
		)
	), false); ?>
 * </example>
 */

class callHelper {
	/**
	* Run & draw into output
	* @param Array $routerInfo - array( 'controller'=>'...', 'action'=>'...', 'module'=>'...', 'disableLayout'=true, ['params'=>array()] )
	*/
	public function run( $routerInfo = array()) {
		$routerInfo['disableLayout'] = isset($routerInfo['disableLayout'])?$routerInfo['disableLayout']:true;
		$routerInfo['breakOnRender'] = false;
		$routerInfo['caller'] = 1;  // 0 - call from internet request, 1 - call local
		
		$application = K_Registry::read( 'bootstrap' )->getApplication();
		$application->executeRequest( $routerInfo, true, false); // execute router with autoRender attribute
	}
	
	/**
	* Run & capture html + cache
	* @param Array $routerInfo - array( 'controller'=>'...', 'action'=>'...', 'module'=>'...', 'disableLayout'=true, ['params'=>array()] )
	* @param Array $cacheArray - array( 'manager'=>$cacheManagerObject, 'id'=>'cacheID', ['tags'=>array()], ['lifetime'=>(int)60])
	* @param bool $fastDrawCacheData - false - disabled, true - enabled, put content to output buffer & NOT RETURN RESULT HTML (return true on OK), works only if you use cache
	* @example
			$this->call->html( 
				array(
					'module'=>'admin',
					'controller'=>'index',
					'action'=>'index',
					'params'=>array(
						'id'=>22
					)
				),
				array(
					'manager' => K_Registry::get('cacheManager')->getCache('unlim'),
					'id'=>'admin_index_index_22_cache_id',
					'tags'=>array(
						'cache-by-call',
						'id-22',
						'admin-index-index-22'
					),
					'lifetime'=>120
				),
				true // on false you can use result HTML, and draw its manually (ONLY IF YOU USE CACHE)
			);
	*/
	public function html( $routerInfo = array(), $cacheArray = null, $fastDrawCacheData = false  ) {
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
		K_Application::get()->executeRequest( $routerInfo, true, false); // execute router with autoRender attribute
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