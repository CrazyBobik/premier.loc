<?php 

/**
 * Class View
 * 
 * Save controller variables
 * Allow access to saved variables from templates
 * Load layout template
 * Load action-view template
 */

define('TEMPLATE_EXTENSION', '.phtml');

class K_View extends StdClass {
	
	protected $_options = array(
					'module'        => '_site',
					'controller'    => 'index',
					'layout'        => 'layout',
					'helpers'       => array(),
					'disableLayout' => false,
					'breakOnRender' => true,
					'disableRender' => false,
					'ajaxOutput'    => null
				);
                
				
	protected $layoutDirectories = array(
					'/{module}/view'
				);
				
	protected $viewDirectories = array(
					'/{module}/view/{controller}'
				);
	
    public $formTemplate = array(
		'formStart'       => '',
		'formEnd'         => '<div style="margin: 0 auto; width: 90%; display: none; opacity: 0.0;" class="nNote nSuccess hideit" id="x_formsuccess_{{formid}}"><p></p></div>',
		'row'             => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight">{{element}}</div><div class="fix"></div></div>',
		'row_submit'      => '{{element}}',
		'row_reset'       => '{{element}}',
		'row_file'        => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
		'row_select'      => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'checkbox'        => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
		'radio'           => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
    	'row_formbuilder' => '{{element}}',
	);			
                
                
	public function __construct( $_options = array() ){
			
		// —менил index представление на название контроллера
		if(!isset($_options['view'])){
		
			$_options['view'] = $_options['controller']; 
		
		}	
		
		$this->_setOptions($_options);
	}
	
	public function _setOptions( $_options = array() ) {
	
		$this->_options = array_merge( $this->_options, $_options ); 

	}
	
	public function _render(){
               if ( $this->_options['disableRender'] ) {
                    if ( $this->_options['breakOnRender'] ) {
                        die() or exit();
                    }
                    return;
                }

                if ( $this->_options['breakOnRender'] ) {
			$headers = K_Application::get()->getHeaders();
			if ( is_array($headers) && count($headers) ) {
				foreach ($headers as $header) {
					header( $header, true ); // @TODO may be error TRUE/FALSE on replace
				}
			}
		}

                if ( !empty($this->_options['ajaxOutput']) ) {
                    echo $this->_options['ajaxOutput'];
                    die() or exit();
                }
        
		if ( is_array($this->_options['helpers']) && count($this->_options['helpers']) ) {
			$viewHelper = K_ViewHelper::get();			
			foreach( $this->_options['helpers'] as $helper ) {
				$viewHelper->loadHelper( $this, $helper );
			}
		}
		
		if ( !$this->_options['disableLayout'] ) {			
			$this->_loadLayout();
		} else {			
			$this->context();
		}
		
		if ( $this->_options['breakOnRender'] ) {
			K_Debug::get()->printCache('DBG');
			die() or exit(); // ;)
		}
    }
	
	public function loadHelper( $helperName ) {
		$viewHelper = K_ViewHelper::get();			
		$viewHelper->loadHelper( $this, $helperName );
	}
	
	protected function _loadLayout() {	
		foreach( $this->layoutDirectories as $layoutDir ) {
			$layoutDir = APP_PATH.str_replace( '{module}', $this->_options['module'], $layoutDir );
			if ( is_file( $layoutDir.'/'.$this->_options['layout'].TEMPLATE_EXTENSION) ) {
				require $layoutDir.'/'.$this->_options['layout'].TEMPLATE_EXTENSION;
				return;
			}
		}
		throw new Exception('Layout '.$this->_options['layout'].TEMPLATE_EXTENSION.' no found in layout directories');
	}
	
	protected function context() {	
	
			foreach( $this->viewDirectories as $viewDir ) {
			
				$viewDir = APP_PATH.str_replace( array('{module}', '{controller}' ), array( $this->_options['module'], $this->_options['controller'] ), $viewDir );
		
				if($this->_options['view'] == strtolower("index")){
		
					echo $this->_options['controller'];
		
					if ( is_file( $viewDir.'/'.$this->_options['controller'].TEMPLATE_EXTENSION) ) {
					
						require $viewDir.'/'.$this->_options['controller'].TEMPLATE_EXTENSION;
						return;
					}
					
					if ( is_file( $viewDir.'/'.'index'.TEMPLATE_EXTENSION) ) {
					
						require $viewDir.'/'.'index'.TEMPLATE_EXTENSION;
						return;
					}
									
				}	else{
				
					if ( is_file( $viewDir.'/'.$this->_options['view'].TEMPLATE_EXTENSION) ) {
					
						require $viewDir.'/'.$this->_options['view'].TEMPLATE_EXTENSION;
						return;
					}
				
				}		
							
			}
			
			K_Debug::get()->addError( 'View '.$this->_options['view'].TEMPLATE_EXTENSION.' no found in view directory' );
	}
	
	public function x_context($view)
	{
		if (is_array($this->_options['helpers']) && count($this->_options['helpers']))
		{
		
			$viewHelper = K_ViewHelper::get();			
			foreach($this->_options['helpers'] as $helper)
			{
				$viewHelper->loadHelper($this, $helper);
			}
		}
	
		$this->_options['view'] = $view;
		$this->context();
	}
	
	public function generateForm($structure, $data, $formData = array('', '', '', 'application/x-www-form-urlencoded',''), $template = false, $extraFields=false,$adminka=true)
	{
		K_jForm::generate($structure, $data, $formData, $this->form, $template, $extraFields,$adminka);
	}
    
   	public function generateClientForm($treeLink , $action)
	{
     $formStructure = Gcontroller::loadClientFormStructure($treeLink);
         
     $this->generateForm($formStructure['form_structure'], (isset($this->type) ? $this->type : array()), array($action, 'post', $this->actionType, 'multipart/form-data',array('class'=>'ajax-form')), $this->formTemplate, array('tree_link'=>$treeLink),$adminka=false);
	}
    
   	public function generateCastomForm($treeLink , $action)
	{
     $formStructure = Gcontroller::loadCastomFromStructure($treeLink);
     $this->generateForm($formStructure['form_structure'], (isset($this->type) ? $this->type : array()), array($action, 'post', $this->actionType, 'multipart/form-data',array('class'=>'ajax-form')), $this->formTemplate, array('tree_link'=>$treeLink),$adminka=false);
	}
    
    
    
    public function generateTypeForm($type , $action)
	{
     $formStructure = Gcontroller::loadTypeStructure($type);
     $this->generateForm($formStructure['form_structure'], (isset($this->type) ? $this->type : array()), array($action, 'post', $this->actionType, 'multipart/form-data',array('class'=>'ajax-form')), $this->formTemplate, array('tree_link'=>$treeLink),$adminka=false);
	}
	
	public function loadFormTemplate($template)
	{
		$this->formTemplate = $template;
	}
}

?>