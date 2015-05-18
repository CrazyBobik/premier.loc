<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_Gui extends Gcontroller {

	/* {public} */
	public $module = 'admin';
	public $helpers = array('form');
	public $formTemplate = array(
		'formStart'   => '',
		'formEnd'     => '<div style="margin: 0 auto; width: 90%; display: none; opacity: 0.0;" class="nNote nSuccess hideit" id="x_formsuccess_{{formid}}"><p></p></div>',
		'row'         => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight">{{element}}</div><div class="fix"></div></div>',
		'row_submit'  => '{{element}}',
		'row_reset'   => '{{element}}',
		'row_wysiwyg' => '<div class="rowElem noborder admin-form-row">{{label}}:<div class="wysywig_block">{{element}}</div></div>',
	);
	
	public $disableRender = true;
	
	public function __construct()
	{
		parent::__construct($this->_options);
		
		$this->view->_setOptions(array('module' => 'admin', 'controller' => 'admin_gui', 'helpers' => $this->helpers));
	}
		
	protected function newGUI()
	{
		$this->tabs['new'] = 'Добавить';
		
		$this->view->formStructure = self::loadFormStructure('/forms/add_element/', get_class(), array('loadTypes' => $this->nodeData));
		$this->view->node = $this->nodeData;
		$this->view->loadFormTemplate($this->formTemplate);
		
		if (!self::f_loadTypes($this->view->node))
		{
			return false;
		}
		
		return $this->x_render('add', $this);
	}
	
	protected function nodeGUI()
	{
		$this->tabs['node'] = 'Элемент';
		
		if (isset($this->nodeData))
		{
			$this->view->formStructure = self::loadFormStructure('/forms/edit_element/');
			$this->view->node = $this->nodeData;
			$this->view->loadFormTemplate($this->formTemplate);
			
			return $this->x_render('node', $this);
		}
		else
		{
			return false;
		}
	}
	
	protected function editGUI()
	{
		$this->tabs['edit'] = 'Редактирование';
	
		if (isset($this->nodeData))
		{
			$typeModelName = 'Type_Model_'.ucfirst($this->nodeData['tree_type']);
			$typeTable = new $typeModelName();
		
        
			$this->view->formStructure = self::loadTypeFormStructure($this->nodeData['tree_id']);
			
			$elementData = $typeTable->select()->where('`type_'.$this->nodeData['tree_type'].'_id`='.(int)$this->nodeData['tree_id'])->fetchRow();
            
           	$elementData = $elementData->toArray();
            
			$rightElementData = array();
			
			foreach ($elementData as $key => $value)
			{
			
            $rightElementData[str_replace('type_'.$this->nodeData['tree_type'].'_', '', $key)] = $value;
		
        	}
			
			$this->view->element = $rightElementData;
			
			$this->view->node = $this->nodeData;
			$this->view->loadFormTemplate($this->formTemplate);
			
			return $this->x_render('edit', $this);
		}
		else
		{
			return false;
		}
	}
	
	public static function f_loadTypes($params = array())
	{
		$result = array();
	
		$typesTable = new K_Tree_Types_Model();
		
		$types = $typesTable->select()->fetchArray();
		$typeClass = 'Type_Controller_'.ucfirst($params['tree_type']);
		
        if ($params['tree_type'] == 'list'){
            
            $list = K_CupItems::getItems($params['tree_id'],$params['tree_type']);
        
            $listTypes = array_map('trim', explode(',', $list[0]['types']));
                
               
         	for ($i = 0, $j = 0; $i < sizeof($types); $i++){
 	      	   if(in_array($types[$i]['type_name'], $listTypes)){
                    
                        $result[$j] = new stdClass();
    					$result[$j]->value = $types[$i]['type_name'];
    					$result[$j]->baseline = 'undefined';
    					$j++;
                        
               }
            }
        }else {       
     		for ($i = 0, $j = 0; $i < sizeof($types); $i++)
    		{
    			if (isset($typeClass::$allowedChildren[0]))
    			{
    				if ((($typeClass::$allowedChildren[0] == 'all') && (sizeof($typeClass::$allowedChildren) == 1)) || (($typeClass::$allowedChildren[0] == 'all') && !in_array($types[$i]['type_name'], $typeClass::$allowedChildren)) || (($typeClass::$allowedChildren[0] != 'all') && in_array($types[$i]['type_name'], $typeClass::$allowedChildren)))
    				{
    					$result[$j] = new stdClass();
    					$result[$j]->value = $types[$i]['type_name'];
    					$result[$j]->baseline = 'undefined';
    					
    					$j++;
    				}
    			}
    		}
		}
		return $result;
	}
}