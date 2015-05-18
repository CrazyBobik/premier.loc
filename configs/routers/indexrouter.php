<?php 

/**
 * Forum Module router
 * /forum/[short-controller-action]/fixed-params
 */

class IndexRouter implements K_Request_Irouter {
    
    /**
	 * @var  string  default protocol for all routes
	 *
	 * @example  'http://'
	 */
	public static $default_protocol = 'http://';

	/**
	 * @var  array   list of valid localhost entries
	 */
	public static $localhosts = array(FALSE, '', 'local', 'localhost');

	/**
	 * @var  string  default action for all routes
	 */
	public static $defaults = array('module'=>'index',
                                    'controller'=>'index',
                                    'action'=>'index',
                                    'params'=>array()
                                    );

	/**
	 * @var  bool Indicates whether routes are cached
	 */
     
    protected static $_routes = array();
    
    // Matches a URI group and captures the contents
	const REGEX_GROUP   = '\(((?:(?>[^()]+)|(?R))*)\)';

	// Defines the pattern of a <segment>
	const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

	// What can be part of a <segment> value
	const REGEX_SEGMENT = '[^/.,;?\n]++';

	// What must be escaped in the route regex
	const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';
    
	public function assemble( $url = null ) {
       
       if ( empty($url) ) { 
            
           $urlArr = parse_url(strtolower($_SERVER['REQUEST_URI']));         
          
      	   $url = trim($urlArr['path'], '/');
            
	   }   
       
       $routes = self::compileRouters(allConfig::$routes);
         
       foreach($routes as $route){  
        
          	if ( preg_match($route['url'], $url, $matches ) ){
          	     
                 		$params = array();
			
            			if ( !empty($matches['params']) ){
            				$p = explode( '/', $matches['params'] );
            				if ( count($p)) {
            					for ($i=0; $i<count($p); $i+=2) {
            						$params[ $p[$i] ] = isset( $p[$i+1] )?$p[$i+1]:null;
            					}
            				}
            			}
                 
                        // обработка дефолтов 
                       
            			$result = array(
            				'module' => isset($matches['module'])? $matches['module']: $route['defaults']['module'],
            				'controller' => isset($matches['controller'])? $matches['controller']:$route['defaults']['controller'],
            				'action' =>	isset($matches['action'])? $matches['action']:$route['defaults']['action'],
            				'params' =>	count($params)> 0 ? $params:$route['defaults']['params']
            			);
                        
                        // загрузка конфигов 
                      
                        if(isset($route['loadconfigs']) && count($route['loadconfigs'])>0){
                            
                            foreach($route['loadconfigs'] as $v){
                         
                               require_once CONFIGS_PATH.'/'.$v.'.php';
                              
                            }  
                                               
                        }
                        return $result;            
 			}  
                   
       }; 
       
       return false;
       
    }
    
   	public static function compileRouters($routes)
	{
		      
        $result = array();      
                      
        foreach($routes as $route){
          
            $compiledRoute = array();
            
            $compiledRoute['url'] = self::compileRegex($route['url'], $route['valids']);
          
            $compiledRoute['defaults'] = array_merge(self::$defaults, $route['defaults']);
          
            $compiledRouteMerge = array_merge($route, $compiledRoute);
          
            $result[] = $compiledRouteMerge;
            
        }   
        
        return  $result;
	}
    
   	public static function compileRegex($uri, array $regex = NULL)
	{
		// The URI should be considered literal except for keys and optional parts
		// Escape everything preg_quote would escape except for : ( ) < >
		$expression = preg_replace('#'.IndexRouter::REGEX_ESCAPE.'#', '\\\\$0', $uri);

		if (strpos($expression, '(') !== FALSE)
		{
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
		}

		// Insert default regex for keys
		$expression = str_replace(array('<', '>'), array('(?P<', '>'.IndexRouter::REGEX_SEGMENT.')'), $expression);

		if ($regex)
		{
			$search = $replace = array();
			foreach ($regex as $key => $value)
			{
				$search[]  = "<$key>".IndexRouter::REGEX_SEGMENT;
				$replace[] = "<$key>$value";
			}

			// Replace the default regex with the user-specified regex
			$expression = str_replace($search, $replace, $expression);
		}

		return '#^'.$expression.'$#uD';
	}
    
}

?>