<?php

/**
 * Application Class
 */

include 'error.php';
include 'loader.php';
include 'globalfunctions.php';

class K_Application{
	
	protected static $instance;
	
	public static $root = '.';
	/**
	 * Options array
	 */
	protected $options = array();
	
	protected $headers = array();
	
	protected $bootstrap;
	
	protected $route;
		
	/**
	 * Constructor
	 */
	public function __construct( $rootPath, $configPath ) {
		if (!self::$instance) {
						
			self::$instance = $this;

			if ( file_exists($configPath) ) {
				$this->options = parse_ini_file( $configPath, true );
			} else {
				throw new Exception('Config file not found.');
			}
			
			if (!empty($this->options['Application']) && !empty($this->options['Application']['bootstrap']) ) {
				$bootsPath = str_replace('[APP_PATH]', APP_PATH, $this->options['Application']['bootstrap']);
				if (file_exists($bootsPath)) {
					require_once( $bootsPath );	
				} else {
					echo 'Bootstrap not found. '.$bootsPath;
				}
			}
			
			if ( is_dir($rootPath) ) {
				self::$root = $rootPath;
			} else {
				throw new Exception('Root dir not found.');
			}
						
			K_Request::init();
		} else {
			return self::instance;
		}
	}
	
	/**
	 * Init bootstrap
	 */
	public function bootstrap() {
		if (empty($bootstrap)) {
			$this->bootstrap = new Bootstrap();
		}
		return $this->bootstrap;
	}
	
	public static function get() {
		return K_Application::$instance;
	}
	
	public function addHeader( $text ) {
		$this->headers[] = $text;
	}
	
	public function setHeaders( $array ) {
		if ( is_array($array) ) {
			$this->headers = $array;
		}
	}
	
	public function getHeaders() {
		return $this->headers;
	}
	
	public function dispatch() {
		
		$this->route = K_Request::route();
	    
        if(empty($this->route)){
            
           K_Request::redirect('/404','404'); 
            
        }
        
		$this->bootstrap->route = $this->route;
		
		$this->route['caller'] = 0; // 0 - call from internet request, 1 - call local
	
		$this->executeRequest( $this->route );
	}
	
	/**
	 * Execute action
	 * @param Array 	$route			array with call attributes as controller, module, action, params, disableLayout etc.
	 * @param Bool		$autoRender		render action after call (not wait controller destroy action)
	 */
    public function executeRequest( &$route, $autoRender = false, $checkAcl=true) {
        
        if ( is_array( $route ) ){
		
		    //$user_roles=array('guests');
			 
			//  exit();
		    //K_Auth::authorize($user, $user_roles );
			// 
            // 
		 
		    if($checkAcl && $route['module']!='site' && $route['module']!='ajax' && $route['module']!='dev' ){
			    K_Access::accessSite($route);
		    }
            
            //  echo '$this->controller = new '.ucfirst($route['module']).'_Controller_'.ucfirst($route['controller']).'( $route );';
		        
            try {

             	  eval('$this->controller = new '.ucfirst($route['module']).'_Controller_'.ucfirst($route['controller']).'( $route );');
  
            }
            catch (Exception $e) {
                
                  if(K_debug::get()->isEnabled() == true){
                    
                     echo  'Ошибка создания контроллера '.ucfirst($route['module']).'_Controller_'.ucfirst($route['controller']).'( $route );';
                     exit();
                     
                  }else{
                      
                     K_Request::redirect('/404', '404');
                    
                  }
              
            }
          
			$this->controller->options( $route );				
			$this->controller->run( $route['action'], null, $autoRender );
		}
	}
}

?>