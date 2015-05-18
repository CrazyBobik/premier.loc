<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Gcontroller extends Controller {
	
	/* {public} */
	public $layout = 'layout';
	public $module = 'default';
	public $tabs = array();
	public $type;
	
	/* {protected} */
	protected $nodeData;
	protected $typeData;
	    
	/* {actions} */
	public function indexAction()
	{
		$this->loadAction();
	}
	
	public function showAction()
	{
		$this->disableRender = true;
		
		if ($this->getParam("type"))
		{
			$this->type = $this->getParam("type");
			
			$guiController = ucfirst($this->module).'_Controller_Gui_'.ucfirst($this->type);
			$gui = new $guiController($node);
			
			$gui->generateGuis($guiController);
		}
	}
	
	public function loadAction()
	{
		$this->disableRender = true;
		
		if ($this->getParam("key"))
		{
			$nodeId = (int)$this->getParam("key");
			
			$this->nodeData = K_Tree::getNode($nodeId);
			$this->type = $this->nodeData['tree_type'];
        
			if(K_access::accessTree($nodeId,true)){
		    	$guiController = ucfirst($this->module).'_Controller_Gui_'.ucfirst($this->type);
    		
            
            	$this->generateGuis($guiController);
            }
            else{
                $this->putAjax("<div style='margin:15px'>Доступ к этому разделу запрещён</div>");               
            }
       
		}
	}
	
	protected function generateGuis($guiController)
	{
       	$activeTab = $this->getParam("activeTab");
        $needTabs = $this->getParam("needTabs");
		$gui = new $guiController($this->nodeData);
		$nodeId=$this->nodeData['tree_id'];
		$classMethods = get_class_methods($gui);
		
		$tabsHeader = '<ul class="tabs">';
      
       
        $addAccess=K_access::accessTree($nodeId,array('add','addremove'),true);
      
		$i = 0;
		foreach ($classMethods as $methodName) {
			if (strpos($methodName, 'GUI') !== false && $gui->$methodName() !== false)
			{
	 	      $tabName=substr($methodName, 0, -3);
            
               // var_dump( $this->tabAction($tabName));
              //  var_dump( K_access::accessTree($nodeId,$this->tabAction($tabName)));
               
                if ($addAccess || K_access::accessTree($nodeId,$this->tabAction($tabName))){
                   	$i++; 
                    if(empty($needTabs) || in_array($tabName,$needTabs)){
        	    		echo '<div class="gui-block tab_content '.$tabName.'" id="tab'.($i + 1).'" >'.$gui->$methodName().'</div>';
    				    $tabsHeader .= '<li '.($tabName == $activeTab ? 'class="activeTab"' :'' ).'><a href="#tab'.($i + 1).'" id="tab-'.$tabName.'", >'.(isset($gui->tabs[substr($methodName, 0, -3)]) ? $gui->tabs[substr($methodName, 0, -3)] : '---').'</a></li>';
                  }
                }
                if($i==0){
                  $this->putAjax("<div style='margin:15px'>Для этого пункта доступен только просмотр");               
                }
			}
		}
		
		$tabsHeader .= '</ul>';
		
		echo $tabsHeader;
	}
    
    protected function tabAction($tabname)
	{
	   switch ($tabname) {
            case 'new':
                return array('add','addremove') ;
            case 'node':
                return array('edit');
            case 'edit':
                return array('edit');
       }
	}
    	
	public static function loadFormStructure($formLink, $formMethodsClass = false, $formMethodsParams = array())
	{
		$treeTable = new K_Tree_Model();
		
		$resultData = '';
		
		$node = $treeTable->select()->where('`tree_link`="'.$formLink.'"')->fetchRow();
		
		if ($node)
		{
			try
			{
				$formTable = new Type_Model_Form();
				
				$formData = $formTable->select()->where('`type_form_id`='.(int)$node['tree_id'])->fetchRow();
				
				$unserializedFormData = unserialize($formData['type_form_content']);
				$unserializedFormData = json_decode($unserializedFormData['form_structure']);
				
				for ($i = 0; $i < sizeof($unserializedFormData); $i++)
				{
					if ($unserializedFormData[$i]->type == 'select') // доработать и для чекбоксов, и для радиобоксов
					{
						if (!empty($unserializedFormData[$i]->values->method) && $formMethodsClass !== false)
						{
							if (method_exists($formMethodsClass, 'f_'.$unserializedFormData[$i]->values->method))
							{
								$className = $formMethodsClass;
								$methodName = 'f_'.$unserializedFormData[$i]->values->method;
								
								$data = $className::$methodName(isset($formMethodsParams[$unserializedFormData[$i]->values->method]) ? $formMethodsParams[$unserializedFormData[$i]->values->method] : array());
								
								$unserializedFormData[$i]->options = $data;
							}
						}
					}
				}
				
				$resultData = array('form_structure' => json_encode($unserializedFormData));
								
				return $resultData;
			}
			catch(Exception $err)
			{
				K_Debug::get()->addError('Model Type_Model_Form not found in type/model directory');
			}
		}
		else
		{
			K_Debug::get()->addError('Form '.$formLink.' was not loaded. Node required');
		}
	}
	
    
    public static function loadClientFormStructure($formLink, $formMethodsClass = false, $formMethodsParams = array())
	{
		$treeTable = new K_Tree_Model();
		
		$resultData = '';
		
		$node = $treeTable->select()->where('`tree_link`="'.$formLink.'"')->fetchRow();
		
		if ($node)
		{
			try
			{
				$formTable = new Type_Model_ClientForm();
				
				$formData = $formTable->select()->where('`type_clientform_id`='.(int)$node['tree_id'])->fetchRow();
				
				$unserializedFormData = unserialize($formData['type_clientform_content']);
				$unserializedFormData = json_decode($unserializedFormData['form_structure']);
				
				for ($i = 0; $i < sizeof($unserializedFormData); $i++)
				{
					if ($unserializedFormData[$i]->type == 'select') // доработать и для чекбоксов, и для радиобоксов
					{
						if (!empty($unserializedFormData[$i]->values->method) && $formMethodsClass !== false)
						{
							if (method_exists($formMethodsClass, 'f_'.$unserializedFormData[$i]->values->method))
							{
								$className = $formMethodsClass;
								$methodName = 'f_'.$unserializedFormData[$i]->values->method;
								
								$data = $className::$methodName(isset($formMethodsParams[$unserializedFormData[$i]->values->method]) ? $formMethodsParams[$unserializedFormData[$i]->values->method] : array());
								
								$unserializedFormData[$i]->options = $data;
							}
						}
					}
				}
				$resultData = array('form_structure' => json_encode($unserializedFormData));
				return $resultData;
			}
			catch(Exception $err)
			{
				K_Debug::get()->addError('Model Type_Model_Form not found in type/model directory');
			}
		}
		else
		{
			K_Debug::get()->addError('Form '.$formLink.' was not loaded. Node required');
		}
	}
    
    
    public static function loadCastomFromStructure($formLink, $formMethodsClass = false, $formMethodsParams = array())
	{
		$treeTable = new K_Tree_Model();
		
		$resultData = '';
		
		$node = $treeTable->select()->where('`tree_link`="'.$formLink.'"')->fetchRow();
		
		if ($node)
		{
			try
			{
			    $typeModelName='Type_Model_'.ucfirst($node['tree_type']);
             
				$formTable = new $typeModelName;
				
				$formData = $formTable->select()->where('`type_'.$node['tree_type'].'_id`='.(int)$node['tree_id'])->fetchRow();
				
				$unserializedFormData = unserialize($formData['type_'.$node['tree_type'].'_content']);
				$unserializedFormData = json_decode($unserializedFormData['form_structure']);
				
				for ($i = 0; $i < sizeof($unserializedFormData); $i++)
				{
					if ($unserializedFormData[$i]->type == 'select') // доработать и для чекбоксов, и для радиобоксов
					{
						if (!empty($unserializedFormData[$i]->values->method) && $formMethodsClass !== false)
						{
							if (method_exists($formMethodsClass, 'f_'.$unserializedFormData[$i]->values->method))
							{
								$className = $formMethodsClass;
								$methodName = 'f_'.$unserializedFormData[$i]->values->method;
								
								$data = $className::$methodName(isset($formMethodsParams[$unserializedFormData[$i]->values->method]) ? $formMethodsParams[$unserializedFormData[$i]->values->method] : array());
								
								$unserializedFormData[$i]->options = $data;
							}
						}
					}
				}
				$resultData = array('form_structure' => json_encode($unserializedFormData));
				return $resultData;
			}
			catch(Exception $err)
			{
				K_Debug::get()->addError('Model Type_Model_Form not found in type/model directory');
			}
		}
		else
		{
			K_Debug::get()->addError('Form '.$formLink.' was not loaded. Node required');
		}
	}
    
    
    
     /**
     * Загружает структуру формы для ноды по её типу итема 
     * 
     * 
     *    
     */
    
	public static function loadTypeFormStructure($treeId, $formMethodsClass = false, $formMethodsParams = array())
	{
		$treeTable = new K_Tree_Model();
 		$resultData = '';
		$node = K_Tree::getNode($treeId);
        //treeTable->select()->where('`tree_id`='.(int)$treeId)->fetchRow();
        return self::loadTypeStructure($node['tree_type'], $formMethodsClass, $formMethodsParams);	
	}
    
    /**
     * Загружает структуру формы для типа итема 
     * 
     * 
     *    
     */
    
  
    public static function loadTypeStructure($type, $formMethodsClass = false, $formMethodsParams = array())
	{
	   if ($type)
		{
			try
			{
				$typesTable = new K_Tree_Types_Model();
				
				$typeData = $typesTable->select()->where('`type_name`="'.$type.'"')->fetchRow();
				
				$unserializedFormData = unserialize($typeData['type_fields']);
				$unserializedFormData = json_decode($unserializedFormData['form_structure']);
				
				for ($i = 0; $i < sizeof($unserializedFormData); $i++)
				{
					if ($unserializedFormData[$i]->type == 'select') // доработать и для чекбоксов, и для радиобоксов
					{
						if (!empty($unserializedFormData[$i]->values->method) && $formMethodsClass !== false)
						{
							if (method_exists($formMethodsClass, 'f_'.$unserializedFormData[$i]->values->method))
							{
								$className = $formMethodsClass;
								$methodName = 'f_'.$unserializedFormData[$i]->values->method;
								
								$data = $className::$methodName(isset($formMethodsParams[$unserializedFormData[$i]->values->method]) ? $formMethodsParams[$unserializedFormData[$i]->values->method] : array());
								
								$unserializedFormData[$i]->options = $data;
							}
						}
					}
				}
				
				$resultData = array('form_structure' => json_encode($unserializedFormData));
								
				return $resultData;
			}
			catch(Exception $err)
			{
				K_Debug::get()->addError('Model K_Tree_Types_Model not found');
			}
		}
		else
		{
			K_Debug::get()->addError('Element '.$treeId.' was not loaded. Node required');
		}
	}
    
    
	public function x_render($viewTemplate)
	{
		$result = '';
	
		if (is_string($viewTemplate))
		{
		
			ob_start();
			
			$this->view->x_context($viewTemplate);
			
			$result = ob_get_contents();
			ob_end_clean();
			
			return $result;
		}
	}
}