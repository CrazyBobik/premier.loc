<?php 

/**
 * Application Controller
 * 
 * <example>
 * class <module name>_<controller name>Controller extends K_Application_Controller {
 * 		public function onInit() {} // run on init controller
 * 		public function <action name>Action() {
 * 			$this->view->pageTitle = "Example";
 * 			$this->render( '<view name in controller views folder>' ); // without this function call default view-template
 * 		}
 * }
 * </example>
 * 
 * Events:
 * 	public function onInit() {}
	public function onRender() {}
	public function onRenderComplete() {}
	public function onDestroy() {}
 */

class K_Controller {
	protected static $instance;
	
	protected $_options = array();
	
	var  $layout = 'layout';
	var  $helpers = array();
	var  $disableLayout = false;
        var  $plugins = array();

        var $disableRender = false;
        var $ajaxOutput = false;
	
	public $MODULE_PATH = '';
	public $MODULE_TEMPLATES_PATH = '';
	
	protected $rendered = false;
	
	protected $view;
	
	/**
	 * Class construct
	 */
	public function __construct( $_options ) {
		self::$instance = $this;
		$this->name = __CLASS__;
		
		$this->options( $_options );

		if ( isset($this->_options['disableLayout']) ) {			
                    $this->disableLayout = $this->_options['disableLayout'];
		}		
        
        if ( isset($this->_options['layout']) ) {			
                    $this->layout = $this->_options['layout'];
		}	
        
        
		$this->view = new K_View( $this->_options );
                $this->view->controller = &$this;
                K_Plugins::callHook('controller.viewCreate', array( 'controller' => &$this, 'view' => &$this->view ) );
		
		if (!isset($this->_options['module'])) $this->_options['module'] = 'default';
		
		$this->MODULE_PATH = APP_PATH.'/'.$this->_options['module'];
		$this->MODULE_TEMPLATES_PATH = APP_PATH.'/'.$this->_options['module'].'/templates';
		
		K_ViewHelper::get()->addDirectory( APP_PATH.'/'.$this->_options['module'].'/helpers' );
		K_ViewHelper::get()->addDirectory( APP_PATH.'/helpers' );

                if ( is_dir( APP_PATH.'/'.$this->_options['module'].'/plugins' ) ) {
                    K_Plugins::addDirectory( APP_PATH.'/'.$this->_options['module'].'/plugins' );
                }
                
                if ( count($this->plugins) ) {   
                    K_Plugins::load( $this->plugins );
                }
        		
        K_Plugins::callHook('controller.beforeInit', array('controller' => &$this) );
		if ( method_exists( $this, 'onInit') ) {			
			$this->onInit();	// event                        
		}
        K_Plugins::callHook('controller.afterInit', array('controller' => &$this));    		
	}
	
	/**
	 * Set options
	 */
	public function options( $_options ) {
		$this->_options = array_merge( $this->_options, $_options );
	} 
	
	/**
	 * @param String	$actionName		name of the action
	 * @param Array		$arguments 		array with arguments for calling action
	 * @param Bool		$autoRender		on true - render action on complete, on false - render action on destroy
	 */
	public function run( $actionName, $arguments = array(), $autoRender = false ) {		
		$this->action = strtolower($actionName);
		$action = $this->_options['action'].'Action';
		if ( method_exists($this, $action ) ) {
			$this->$action( $arguments );
			if ( $autoRender == true ) {				
				$this->render();
			}
		} else {
		    if(false){
		      
        		throw new Exception('Action "'.$this->_options['action'].'" not found in "'.$this->_options['controller'].'" controller');
           
            }else{
                
                K_Request::redirect('/404', '404');
                
            }
		}
	}
	
	public function getParam( $paramName ) {
		if ( isset($this->_options['params'][ $paramName ]) ) { // @TODO Security test for not urldecoded data
                        if ( is_string( $this->_options['params'][ $paramName ] ) ) {
                            return urldecode( $this->_options['params'][ $paramName ] );    
                        } else {
                            return $this->_options['params'][ $paramName ];    
                        }			
		}
		return null;
	}
        
    /**
     * Example 
     *  echo getOption('caller') == 0 ? 'its render from web' : 'its render local';
     * 
     * @param type $key
     * @return type 
     */
    public function getOption( $key ) {
        if ( isset($this->_options[ $key ]) ) {
            return $this->_options[ $key ];
        }
        return null;
    }

	public function setParam( $paramName, $paramValue ) {
		$this->_options['params'][ $paramName ] = $paramValue;
	}
	
	/**
	 * Render template
	 */
	public function render( $viewTemplate = null ) {
         
          if($this->rendered){
       	          return;
          }
            
    		if ( method_exists( $this, 'onRender') ) {
    			$this->onRender();	// event
    		}
                    K_Plugins::callHook('controller.onRender', array('controller' => &$this));
                 
    
    		if (isset($this->_options['action']))
    		{
    			if ( empty($viewTemplate) ) {
    				$viewTemplate = $this->_options['action'] != strtolower("index")? $this->_options['action'] : $this->_options['controller'];
    			}
    		}
    		else
    		{
    			$this->_options['action'] = '';
    			$this->disableRender = true;
    		}
    				
    		$this->view->_setOptions( 
    			array_merge( $this->_options,
    				array(
    					'view' => $viewTemplate,
    					'layout' => $this->layout,
    					'helpers' => $this->helpers,
    					'disableLayout' => $this->disableLayout,
                                            'disableRender' => $this->disableRender,
                                            'ajaxOutput' => $this->ajaxOutput
    				)
    			)
    		);
    
    		$this->rendered = true;		
    		$this->view->_render();
    				
    		if ( method_exists( $this, 'onRenderComplete') ) {
    			$this->onRenderComplete();	// event
    		}
                    K_Plugins::callHook('controller.onRenderComplete', array('controller' => &$this));
       
	}
	
	/**
	 * Class destroy
	 */
	public function __destruct() {		
		if ( !$this->rendered ) {			
			$this->render();
		}
		
                K_Plugins::callHook('controller.beforeDestroy', array('controller' => &$this));
		if ( method_exists( $this, 'onDestroy') ) {                        
                    $this->onDestroy();	// event                        
		}
                K_Plugins::callHook('controller.afterDestroy', array('controller' => &$this));
	}
	
	/**
	 * Disable default view, layout & debug
	 * Render json in
	 * @param type $data 
	 */
	public function putJSON( $data ) {
		$this->disableLayout = true;
		$this->rendered = true;
		die( json_encode($data) );
	}
        

	public function putAjax( $data ) {
		$this->disableLayout = true;
		$this->rendered = true;
		die( $data );
	}
    
}

?>