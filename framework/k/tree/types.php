<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Tree_Types {

    public static function add($typeName, $typeDesc, $fields, $allowedChildren, $allowedParents, $module = 'type', $generateClasses = true, $formbuilderStructure = false, $createHMVC = false, $seo = false)
	{
		$typesTable = new K_Tree_Types_Model();
	
		if (!preg_match('/[a-z0-9.-]+/s', $typeName))
		{
			throw new Exception('Wrong type name: '.$typeName);
		}
				
		if (!is_array($fields) || empty($fields))
		{
			if (json_decode($fields) != null)
			{
				$fields = json_decode($fields);
				$fields = self::objectToArray($fields);
			}
			else
			{
				$fields = array();
				//throw new Exception('Cannot create empty type: '.$typeName);
			}
		}
		
		if (!is_array($allowedChildren))
		{
			
            $allowedChildren = (array)$allowedChildren;
			
			foreach ($allowedChildren as $key => $value)
			{
				if ($value == 'Все')
				{
					$allowedChildren[$key] = 'all';
				}
				
				if ($value == 'Нет')
				{
					$allowedChildren = array();
					break;
				}
			}
			
			//throw new Exception('Childrens must be array: '.$typeName);
		}
		
		if (!is_array($allowedParents))
		{
			$allowedParents = (array)$allowedParents;
			
			foreach ($allowedParents as $key => $value)
			{
				if ($value == 'Все')
				{
					$allowedParents[$key] = 'all';
				}
				
				if ($value == 'Нет')
				{
					$allowedParents = array();
					break;
				}
			}
		
			//throw new Exception('Parents must be array: '.$typeName);
		}
		
		if (!is_dir(APP_PATH.'/'.$module))
		{
			throw new Exception('Wrong module directory: '.$typeName);
		}
		
		$pageExists = $typesTable->count(
			K_Db_Select::create()
				->where('`type_name` = "'.$typeName.'"')
		);
		
		if ($pageExists > 0)
		{
			throw new Exception('Current type already exists: '.$typeName);
		}
		
		$time = time();
		
		$insertIntoTypesData = array(
        
			'type_name'     => $typeName,
			'type_desc'     => $typeDesc,
			'type_fields'   => serialize($formbuilderStructure),
			'types_module'  => $module,
			'type_added'    => $time,
			'type_modified' => $time,
            
		);
		
		$insertId = $typesTable->save($insertIntoTypesData);
		
		$newTableName = 'type_'.$typeName;
        
        $seoFields = array('title', 'keys', 'h1', 'desc');
    	
        $sql = 'CREATE TABLE IF NOT EXISTS`' . $newTableName . '` (
			`' . $newTableName . '_id` INT UNSIGNED NOT NULL ,`' . $newTableName . '_pid` INT UNSIGNED NOT NULL,';
	    
        // если сео тип то добавляем поля с префиксом SEO
        
  		foreach ($fields as $fieldId => $field)
		{
	
    		if ($field['type'] == 'submit' || $field['type'] == 'reset') continue;
	    	$sql .= '`' . $newTableName . '_' . $field['values']['name'] . '` ' . self::setType($field['type'],$field['vlds']) . ' NOT NULL ,';
    
    	}
        
        // если сео тип то добавляем поля с префиксом SEO
        
        if($seo){
            
            foreach ($seoFields as  $field)
    		{
    			$sql .= '`' . 'seo' . '_' . $field . '` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,';
      		}
            
      	}
        
		$sql .= 'PRIMARY KEY (`' . $newTableName . '_id`)
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;';

		$query = new K_Db_Query;

		$query->q($sql);
        
		if($generateClasses)
		{
		  
			self::generateModel($typeName, $module);
			self::generateController($typeName, $module, $allowedChildren, $allowedParents, $fields);
			self::generateGUI($typeName, $module, $allowedChildren, $allowedParents, $fields, $seo);
            
 		}
        
        if($createHMVC)
        {
            
          	self::generateTypeBlockController($typeName, array( 'type'=>ucfirst($typeName) ));
			self::generateTypeBlockTemplates($typeName);
            
	    }
        
  	}
	
	public static function delete($typeName)
	{
		$typesTable = new K_Tree_Types_Model();
	
		$typesTable->select()->where('`type_name` = "'.$typeName.'"')->remove();
		
		$query = new K_Db_Query();
		$query->q('DROP TABLE IF EXISTS `type_'.$typeName.'`');
        
        unlink(APP_PATH.'/typebloks/controller/'.$typeName.'.php');
        unlink(APP_PATH.'/typebloks/templates/'.$typeName);
        
        
        unlink(APP_PATH.'/types/controller/'.$typeName.'.php');
        unlink(APP_PATH.'/types/model/'.$typeName.'.php');
        unlink(APP_PATH.'/admin/controller/gui/'.$typeName.'.php');
	}
	
	public static function update($typeName, $desc, $fields, $formbuilderStructure = false)
	{
	   
		if (!is_array($fields) || empty($fields))
		{
			if (json_decode($fields) != null)
			{
				$fields = json_decode($fields);
				$fields = self::objectToArray($fields);
			}
			else
			{
				throw new Exception('Cannot modify fields in: '.$typeName);
			}
		}
	
		$typesTable = new K_Tree_Types_Model();
		
		$typesTable->update(array(
				'type_desc' => $desc,
				'type_fields' => serialize($formbuilderStructure),
				'type_modified' => time(),
			), '`type_name` = "'.$typeName.'"');
			
		$query = new K_Db_Query();
		$columns = $query->q('SHOW COLUMNS FROM `type_'.$typeName.'`');
		
		$rightFields = array();
		$usedFields = array(
			0 => 'type_'.$typeName.'_id'
		);
		
		$i = 0;
        
		foreach ($fields as $fieldId => $field)
		{
			if ($field['type'] == 'submit' || $field['type'] == 'reset') continue;

			$rightFields[$i] = 'type_'.$typeName.'_'.$field['values']['name'];
			$rightFieldParams[$i] = $field;
			
			$i++;
		}
		
		$idExists = false;
		
		foreach($columns as $key => $column)
		{
			$columns[$key] = $column->toArray();
			
			if ((!in_array($columns[$key]['Field'], $rightFields)) && ($columns[$key]['Field'] != 'type_'.$typeName.'_id') && ($columns[$key]['Field'] != 'type_'.$typeName.'_pid'))
			{
				$query->q('ALTER TABLE `type_'.$typeName.'` DROP `'.$columns[$key]['Field'].'`');
			}
			else
			{
				$usedFields[] = $columns[$key]['Field'];
			}
			
			if ($columns[$key]['Field'] == 'type_'.$typeName.'_id')
			{
				$idExists = true;
			}
		}
		
		if (!$idExists)
		{
			$query->q('ALTER TABLE `type_'.$typeName.'` ADD `type_'.$typeName.'_id` INT UNSIGNED NOT NULL FIRST');
		}
		
		foreach ($rightFields as $key => $value)
		{
			if (!in_array($value, $usedFields))
			{
				$usedFieldsValues = $usedFields;
	     
         	echo $qui='ALTER TABLE `type_'.$typeName.'` ADD `'.$value.'` '.self::setType($rightFieldParams[$key]['type'],$rightFieldParams[$key]['vlds']).' NOT NULL AFTER `'.end($usedFieldsValues).'`';
				
				$query->q($qui);
                $usedFields[] = 'type_'.$typeName.'_'.$value;
			}
		}
		
		return true;
	}
	
	private static function generateModel($typeName, $module)
	{
		$code  = '<?php defined(\'K_PATH\') or die(\'DIRECT ACCESS IS NOT ALLOWED\');'."\n\n";
		
		$code .= 'class '.ucfirst($module).'_Model_'.ucfirst($typeName).' extends Model {'."\n";
			$code .= "\t".'public $name = \'type_'.$typeName.'\';'."\n";
			$code .= "\t".'public $primary = \'type_'.$typeName.'_id\';'."\n";
			$code .= "\t".'public $foreign = array('."\n";
				$code .= "\t\t".'\'K_Tree_Model\' => array('."\n";
					$code .= "\t\t\t".'\'type_'.$typeName.'_id\' => array('."\n";
						$code .= "\t\t\t\t".'\'key\' => \'tree_id\','."\n";
						$code .= "\t\t\t\t".'\'type\' => K_LINKTYPE_ONE_ONE,'."\n";
						$code .= "\t\t\t\t".'\'delete\' => \'cascade\','."\n";
						$code .= "\t\t\t\t".'\'update\' => \'none\','."\n";
					$code .= "\t\t\t".')'."\n";
				$code .= "\t\t".')'."\n";
			$code .= "\t".');'."\n";
		$code .= '}'."\n";
		
		if (!is_dir(APP_PATH.'/'.$module.'/model'))
		{
			throw new Exception('Model directory for new type not exists: '.$typeName.' - '.$module);
		}
		
		$f = @fopen(APP_PATH.'/'.$module.'/model/'.$typeName.'.php', 'w');
		fwrite($f, $code);
		fclose($f);
	}
 	
	private static function addQuotes($value)
	{
		return '\''.$value.'\'';
	}
	
	private static function generateGUI($typeName, $module, $allowedChildren, $allowedParents, $fields, $seo = false)
	{
		$code  = '<?php defined(\'K_PATH\') or die(\'DIRECT ACCESS IS NOT ALLOWED\');'."\n\n";
		
		$code .= 'class Admin_Controller_Gui_'.ucfirst($typeName).' extends Admin_Controller_Gui {'."\n\n\t".'
				
                    public function __construct($nodeData)'."\n\t".'
    				{'."\n\t\t".'
    					parent::__construct($this->_options);'."\n\n\t\t".'
    					
    					$this->nodeData = $nodeData;'."\n\t".'
    				}'."\n";
			  
            
        if($seo){
            $code .= 'public function seoGUI()'."\n\t".'
        				{'."\n\t\t".'
                        
                         	$this->tabs["seo"] = "Сео оптимизация";'."\n\t".'
                        
                           return <<< HTML
         
                                 <div class="b-padded mainForm"> 
                                       <div id="flash-msg-nNote" class="nNote hideit" style="display: none;"><p></p></div>
                                
                                  
                                 <form action="/admin/blogs/settags/" class="ajax-form" method="post">
                                     <div class="rowElem noborder admin-form-row">
                                                      <label>
                                                        Дескриптионы:
                                                      </label>
                                                      <div class="formRight">
                                                        <input type="text" name="descriptions" id="add-new-tag-name" />
                                                      </div>
                                                      <div class="fix"></div>
                                                      <input type="button" value="Добавить" id="add-new-tag" class="b-button greyishBtn submitForm">
                                                      
                                      </div>
                                      
                                      <div class="rowElem noborder admin-form-row">
                                                      <label>
                                                        Кейвордсы:
                                                      </label>
                                                      <div class="formRight">
                                                        <input type="text" name="keywords" id="add-new-tag-name" />
                                                      </div>
                                                      <div class="fix"></div>
                                                      <input type="button" value="Добавить" id="add-new-tag" class="b-button greyishBtn submitForm">
                                      </div>
                                      
                                      <div class="rowElem noborder admin-form-row">
                                                      <label>
                                                        H1:
                                                      </label>
                                                      <div class="formRight">
                                                        <input type="text" name="h1" id="add-new-tag-name" />
                                                      </div>
                                                      <div class="fix"></div>
                                                      <input type="button" value="Добавить" id="add-new-tag" class="b-button greyishBtn submitForm">
                                     </div>
                                     
                                     <div class="rowElem noborder admin-form-row">
                                                      <label>
                                                        H1:
                                                      </label>
                                                      <div class="formRight">
                                                        <input type="text" name="new-teg" id="add-new-tag-name" />
                                                      </div>
                                                      <div class="fix"></div>
                                                      <input type="button" value="Добавить" id="add-new-tag" class="b-button greyishBtn submitForm">
                                     </div>
                                 
                                 
                                         <input type="hidden" name="this_key"  value="{$this->nodeData['.'"tree_id"'.']}" />
                                         <input type="submit" value="Сохранить сео настройки" id="save_button" class="b-button greyishBtn submitForm">
                                     
                                  </form>
                                 </div>
                    HTML;
                    
                    }'."\n";
        }
   
        $code .='}';
   
   		if (is_dir(APP_PATH.'/'.$module.'/controller'))
		{
			$f = @fopen(APP_PATH.'/admin/controller/gui/'.$typeName.'.php', 'w');
			fwrite($f, $code);
			fclose($f);
		}
        
 	}
	
	private static function generateController($typeName, $module, $allowedChildren, $allowedParents, $fields)
	{
		$code  = '<?php defined(\'K_PATH\') or die(\'DIRECT ACCESS IS NOT ALLOWED\');'."\n\n";
		
		$code .= 'class '.ucfirst($module).'_Controller_'.ucfirst($typeName).' extends Controller {'."\n\n";
			$code .= "\t".'/* {public} */'."\n";
			$code .= "\t".'public $layout = \'layout\';'."\n";
			$code .= "\t".'public static $allowedChildren = array('.implode(', ', array_map('self::addQuotes', $allowedChildren)).');'."\n";
			$code .= "\t".'public static $allowedParents = array('.implode(', ', array_map('self::addQuotes', $allowedParents)).');'."\n";
			$code .= "\t".'public static $fields = array('.implode(', ', array_map('self::addQuotes', array_keys($fields))).');'."\n\n";
			
			$code .= "\t".'/* {private} */'."\n";
			$code .= "\t".'private $type'.ucfirst($typeName).'Table;'."\n\n";
			
			$code .= "\t".'public function onInit()'."\n";
			$code .= "\t".'{'."\n";
				$code .= "\t\t".'$this->type'.ucfirst($typeName).'Table = new '.ucfirst($module).'_Model_'.ucfirst($typeName).'();'."\n\n";
			$code .= "\t".'}'."\n\n";
			
			$code .= "\t".'/* {actions} */'."\n";
			$code .= "\t".'public function indexAction()'."\n";
			$code .= "\t".'{'."\n";
				$code .= "\t\t".'$this->showAction();'."\n";
			$code .= "\t".'}'."\n\n";
			
			$code .= "\t".'public function showAction()'."\n";
			$code .= "\t".'{'."\n";
				$code .= "\t\t".'if ($this->getParam(\'link\'))'."\n";
				$code .= "\t\t".'{'."\n";
					$code .= "\t\t\t".'$result = $this->type'.ucfirst($typeName).'Table->select()->where(\'`tree_link` = \'.$this->getParam(\'link\'))->fetchRow()->toArray();'."\n";
					$code .= "\t\t\t".'$this->render(\'type_'.$typeName.'_item\');'."\n";
				$code .= "\t\t".'}'."\n";
				$code .= "\t\t".'else'."\n";
				$code .= "\t\t".'{'."\n";
					$code .= "\t\t\t".'$result = $this->type'.ucfirst($typeName).'Table->select()->fetchArray();'."\n";
					$code .= "\t\t\t".'$this->render(\'type_'.$typeName.'\');'."\n";
				$code .= "\t\t".'}'."\n";
			$code .= "\t".'}'."\n\n";
			
			$code .= "\t".'public function createAction()'."\n";
			$code .= "\t".'{'."\n";
				$code .= "\t\t".'$valuesToAdd = array();'."\n";
				$code .= "\t\t".'if (isset($_POST) && !empty($_POST))'."\n";
				$code .= "\t\t".'{'."\n";
					$code .= "\t\t\t".'foreach ($_POST as $key => $value)'."\n";
					$code .= "\t\t\t".'{'."\n";
						$code .= "\t\t\t\t".'if (in_array($key, $this->fields))'."\n";
						$code .= "\t\t\t\t".'{'."\n";
							$code .= "\t\t\t\t\t".'$valuesToAdd[$key] = $value;'."\n";
						$code .= "\t\t\t\t".'}'."\n";
					$code .= "\t\t\t".'}'."\n\n";
					
					$code .= "\t\t\t".'$insertId = $this->type'.ucfirst($typeName).'Table->save($valuesToAdd);'."\n";
				$code .= "\t\t".'}'."\n";
			$code .= "\t".'}'."\n\n";
			
			$code .= "\t".'public function updateAction()'."\n";
			$code .= "\t".'{'."\n";
				$code .= "\t\t".'$valuesToUpdate = array();'."\n";
				$code .= "\t\t".'if (isset($_POST) && !empty($_POST) && $this->getParam(0))'."\n";
				$code .= "\t\t".'{'."\n";
					$code .= "\t\t\t".'foreach ($_POST as $key => $value)'."\n";
					$code .= "\t\t\t".'{'."\n";
						$code .= "\t\t\t\t".'if (in_array($key, $this->fields))'."\n";
						$code .= "\t\t\t\t".'{'."\n";
							$code .= "\t\t\t\t".'$valuesToUpdate[$key] = $value;'."\n";
						$code .= "\t\t\t\t".'}'."\n";
					$code .= "\t\t\t".'}'."\n\n";
					
					$code .= "\t\t\t".'$insertId = $this->type'.ucfirst($typeName).'Table->update($valuesToUpdate, \'`tree_id`= \'.$this->getParam(0).\'\');'."\n";
				$code .= "\t\t".'}'."\n";
			$code .= "\t".'}'."\n\n";
			
			$code .= "\t".'public function deleteAction()'."\n";
			$code .= "\t".'{'."\n";
				$code .= "\t\t".'if ($this->getParam(0))'."\n";
				$code .= "\t\t".'{'."\n";
					$code .= "\t\t\t".'$this->type'.ucfirst($typeName).'Table->select()->where(\'`tree_id` = \'.$this->getParam(0))->remove();'."\n";
				$code .= "\t\t".'}'."\n";
			$code .= "\t".'}'."\n\n";
		$code .= '}'."\n";
		
		if (!is_dir(APP_PATH.'/'.$module.'/controller'))
		{
			throw new Exception('Controller directory for new type not exists: '.$typeName.' - '.$module);
		}
		
		$f = @fopen(APP_PATH.'/'.$module.'/controller/'.$typeName.'.php', 'w');
		fwrite($f, $code);
		fclose($f);
	}
    
    
    // Генераторы HMVC блоков для типов 
    
    private static function generateTypeBlockController($typeName,$tags)
	{
                
        $render = new K_TemplateRender(null,  null, $left = '<%', $right = '%>' );
        $render->setTags($tags);
           
        $render->fromFile(APP_PATH.'/admin/phptemplates/typeblock/controller.ptpl');
  		
		if (!is_dir(APP_PATH.'/typebloks/controller'))
		{
			throw new Exception('Controller directory for new type not exists: '.$typeName);
		}
		
        $code=$render->assemble();
        
		$f = @fopen(APP_PATH.'/typebloks/controller/'.$typeName.'.php', 'w');
		fwrite($f, $code);
		fclose($f);
	}
    
    private static function generateTypeBlockTemplates($typeName,$tags)
	{
        if (!is_dir(APP_PATH.'/typebloks/templates'))
        {
	       	throw new Exception('Templates directory for new typeblock not exists: '.$typeName);
        }
        mkdir(APP_PATH.'/typebloks/templates/'.$typeName, 0766);
        
        $render = new K_TemplateRender(null,  null, $left = '<%', $right = '%>' );
        $render->setTags($tags);
           
        $render->fromFile(APP_PATH.'/admin/phptemplates/typeblock/item.ptpl');
        $code=$render->assemble();
         
		$f = @fopen(APP_PATH.'/typebloks/templates/'.$typeName.'/item.php', 'w');
		fwrite($f, $code);
		fclose($f);
           
        $render->fromFile(APP_PATH.'/admin/phptemplates/typeblock/list.ptpl');
        $code=$render->assemble();
         
		$f = @fopen(APP_PATH.'/typebloks/templates/'.$typeName.'/list.php', 'w');
		fwrite($f, $code);
		fclose($f);
 	}
    
    private static function setType($type,$vlds)
	{
	   if(in_array("int",$vlds)){
        return 'INT';
   	   }elseif(in_array("float",$vlds)){
       	return 'FLOAT';
       }elseif(in_array("length40",$vlds)){
       	return 'VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci';
       }elseif(in_array("length255",$vlds)){
       	return 'VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci';
       }elseif(in_array("date",$vlds)){
       	return 'DATE';
       }elseif(in_array("time",$vlds)){
       	return 'TIME';
       }elseif(in_array("datetime",$vlds)){
       	return 'DATETIME';
       }else{
     		switch ($type)
    		{
    			case 'textarea':
    			case 'wysiwyg':
    		 	case 'formbuilder': 
                 case 'multifile':
    			    return 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
                case 'hidden':
                case 'radio':
    			case 'checkbox':
                case 'select':
    			case 'input_text':
    			case 'file':	
           		case 'password':
    				return 'VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci';
    			default: throw new Exception('Wrong type for new type field: '.$type);
    		}
      } 
  }
	
	static public function objectToArray($obj)
	{
		$arr = (is_object($obj))?
			get_object_vars($obj) :
			$obj;

		foreach ($arr as $key => $val) {
			$arr[$key] = ((is_array($val)) || (is_object($val)))?
				self::objectToArray($val) :
				$val;
		}

		return $arr;
	}
}