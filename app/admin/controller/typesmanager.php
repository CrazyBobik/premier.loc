<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_Typesmanager extends Controller {

    public $helpers = array('form');
    public $formTemplate = array(
        'formStart' => '',
        'formEnd' => '<div style="margin: 0 auto; width: 90%; display: none; opacity: 0.0;" class="nNote nSuccess hideit" id="x_formsuccess_{{formid}}"><p></p></div>',
        'row' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight">{{element}}</div><div class="fix"></div></div>',
        'row_submit' => '{{element}}',
        'row_reset' => '{{element}}',
        'row_file' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'row_select' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'row_formbuilder' => '{{element}}',
        );

  public function onInit() {
	
                $this->formDictionary = new K_Dictionary();
                $this->formDictionary->loadFromIni( ROOT_PATH.'/configs/forms/errors.txt');
                K_Validator::setDefaultDictionary( $this->formDictionary );
		      	//	$this->view->bigtable = true;
				
   }      
   
    
    /// вывод всех топов
    public function indexAction(){
        
        $this->view->title = 'Менеджер типов';
         
        $this->view->headers = array(array('title' => 'Менеджер типов'),
                                     array('title' => 'Инструменты разработчика',
                                           'href' => '/admin/devtools/'
                                          )
                                    );     
   
        $typesTable = new K_Tree_Types_Model();
        $types = $typesTable->select()->fetchArray();

        for ($i = 0; $i < sizeof($types); $i++) {
            $fields = (array )json_decode($types[$i]['type_fields']);
            $contFields = 0;

            if (isset($fields['form_structure'])) {
                $decodedFormStructure = json_decode($fields['form_structure']);
                $contFields = sizeof($decodedFormStructure);
            }

            $types[$i]['type_fields_count'] = (empty($types[$i]['type_fields']) ? '0' : $contFields);
        }

        $this->view->types = $types;
        
        $this->render('show_types');
   
        
    }
    

     /// добавление нового типа
    public function addAction(){
        
        $query = new Query;
        
        $this->view->title = 'Добавление нового типа';
        $this->view->header = 'Добавление нового типа';

        $this->view->headers = array(
                                    array('title' => 'Менеджер типов',
                                          'href' => '/admin/typesmanager/'
                                          ),
                                    array('title' => 'Добавление нового типа'));             

        $this->view->formStructure = Gcontroller::loadFormStructure('/forms/add_type/', get_class());
         
        $this->view->types = $query->q('SELECT type_name FROM types ORDER BY type_name');      

        $this->view->actionType = 'create';

        $this->render('add_edit_type');
        
    }
    
    /// загрузка типов в таблицу
    public function loadtypesAction(){
        
        $query = new Query;
        
        $this->view->title = 'Добавление нового типа';
        $this->view->header = 'Добавление нового типа';

        $page = intval($_POST['page']);
        $onPage = intval($_POST['onPage']);
         
           if($page){ 
                
                if (!$onPage){
                    
                   $onPage = 10; 
                    
                }
                
                $start = $page * $onPage - $onPage;
           
            }else
            {
                
                $start = 0;
                $page = 1;
                $onPage = 10;
            
            }  
           
            if(isset($_POST['name'])){
               $where[] = 'type_name Like ' . K_Db_Quote::quote('%'.$_POST['name'].'%');
            }
            
            if(isset($_POST['desc'])){
               $where[] = 'type_desc Like '. K_Db_Quote::quote('%'.$_POST['desc'].'%');
            }
                  
            if($where && count($where)){
                
               $where = ' WHERE ' . implode(' AND ', $where);
               
            }
        
            $query = new Query; 
    	   
            $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM types $where order by type_name LIMIT $start, $onPage";
      
            $itemsRes = $query->q($sql);  
                  
            $sql ="SELECT FOUND_ROWS() as countItems;";
           
            $countItems = $query->q($sql);   
            
            $countItems = $countItems[0]['countItems'];
            
            $items = array();  
       
            foreach($itemsRes as $v){
                    
    				$itemRow['type_id'] = $v['type_id'];
    				$itemRow['name'] = htmlspecialchars($v['type_name']); 
    				$itemRow['desc'] = htmlspecialchars($v['type_desc']);
    			   	$items[] = $itemRow;    
                       
            }        
              
            $returnJson = array('error' => false,
                                'items' => $items,
                                'countItems' => $countItems
                               );
                           
            $this->putJSON( $returnJson );
    }
 
   // редактирование типа
 
    public function editAction(){
         
        $this->view->title = 'Добавление нового типа';
        $this->view->header = 'Добавление нового типа';
        
        $this->view->headers = array(array('title' => 'Добавление нового типа'),
                                     array('title' => 'Инструменты разработчика',
                                           'href' => '/admin/devtools/'
                                          )
                                    );             

        $typesTable = new K_Tree_Types_Model();
        $typeId = $this->getParam('id');
        $type = $typesTable->select()->where('`type_id`=' . (int)$typeId)->fetchRow();

        $this->view->type = $type->toArray();
        $this->view->formStructure = Gcontroller::loadFormStructure('/forms/edit_type/', get_class());
        $this->view->loadFormTemplate($this->formTemplate);
        $this->view->actionType = 'update';

        $this->render('edit_type');
    }


    /// удаление типа и всех элементов(нод дерева) данного типа.
    
    public function deleteAction() {
        
        $this->disableRender = true;

        $typeName = $this->getParam('name');

        K_Tree_Types::delete($typeName);

        K_cupTree::dTypeNodes($typeName);
       
        echo 'OK';
        
    }
    
    /// импорт типа 
    
    public function importtypeAction(){
            
        $form = new K_Form();
         
        if ($form->hasFiles()){
            
            $typeFiles = $form->getFiles();
            
            //создаём папку если такой нет.
            
            if (!file_exists( ROOT_PATH.'/cache/temp/')) {
                
               mkdir( ROOT_PATH.'/cache/temp/', 0777, true);
               
            } 
        
            $filePathData = $form->moveUploadedFile('type_package', ROOT_PATH.'/cache/temp/', 'loaded' , true);
            
            $zip = new ZipArchive;
             
            if ($zip->open($filePathData['path']) === true){
          
                //загрузка конфига пакета типа
                $config = $zip->getFromName('config.json');
                 
                $configArray = json_decode($config, true);
              
                if(!$configArray['typeName']){
                    
                        $returnJson['error'] = true;       
                        $returnJson['msg'] = 'Ошибка конфигурационного файлы';     
                        $this->putJSON($returnJson);
                        
                } 
                
                // var_dump($configArray['typeName']);
                
                $typeName = $configArray['typeName'];
                
               $typeModel = $zip->getFromName( $typeName.'_model.php' );
               $typeController = $zip->getFromName( $typeName.'_controller.php' );
               $typeGui = $zip->getFromName( $typeName.'_gui.php' );
               $typeIcon = $zip->getFromName( $typeName.'.png' );
                
                $typeModelPath = ROOT_PATH.'/application/type/model/'.$typeName.'.php';
                $typeControllerPath = ROOT_PATH.'/application/type/controller/'.$typeName.'.php';
                $typeGuiPath = ROOT_PATH.'/application/admin/controller/gui/'.$typeName.'.php';
                
                $typeIconPath = ROOT_PATH.'/www/adm/img/tree/'.$typeName.'.png';
               
                if(file_exists($typeModelPath)){
                    unlink($typeModelPath);
                } 
               
                file_put_contents($typeModelPath, $typeModel);    
                
                if(file_exists($typeControllerPath)){
                    unlink($typeControllerPath);
                }
                file_put_contents($typeControllerPath, $typeController);   
                           
                if(file_exists($typeGuiPath)){
                    unlink($typeGuiPath);
                }
                file_put_contents($typeGuiPath, $typeGui);
                
                if(file_exists($typeIconPath)){
                    unlink($typeIconPath);
                }
                
                file_put_contents($typeIconPath, $typeIcon);
                
                $typeRow = $zip->getFromName( 'typerow.json' );
                $typetable = $zip->getFromName( 'typetable.sql' ); ///@todo добавить защиту от sql инекций  
                
                //var_dump($typerow); 
                
                $query = new K_Db_Query;   
                
                $query->q($typetable);
                
                $typeRow = json_decode($typeRow, true);
                $typeModel = new Admin_Model_Type;
              
                //var_dump($typeRow); 
                    
                $typeModel->save($typeRow);  
                
                $zip->addFromString('typerow.json', json_encode($typeRow)); // строка типа из таблицы типов
                $zip->addFromString('typetable.sql', $typeTable['Create Table']); // структура таблицы типа 
                
                $zip->close();
            
            }else{
            
               $returnJson['error'] = true;       
               $returnJson['msg'] = 'Не получилось открыть архив, возможно он повреждён';     
               $this->putJSON($returnJson);
          
            };
        
            $typeRow = '<tr id="type-row-'.$typeRow['type_id'].'" class="type-row" rel="'.$typeRow['type_name'].'"><td >'.$typeRow['type_name'].'</td><td>'.$typeRow['type_desc'].'</td><td><a href="/admin/typesmanager/edit/id/'.$typeRow['type_id'].'" class="type_edit"></a><a title="Вы действительно хотите удалить данный тип?" href="javascript:void(0);" class="type_delete" rel="'.$typeRow['type_name'].'"></a><a onclick="return !window.open(\\\'/admin/typesmanager/exporttype/typeid/'.$typeRow['type_id'].'/\\\')" title="Экспортировать тип" href="/admin/typesmanager/exporttype/typeid/'.$typeRow['type_id'].'/" class="type_export" rel="'.$typeRow['type_id'].'"></a></td></tr>'; 
        
            $returnJson['error'] = false;       
            $returnJson['msg'] = 'Тип успешно добавлен'; 
                
            $returnJson['callback'] = 'function callback(){
                                             $('."'".'#acl-users tbody'."'".').prepend('."'".$typeRow."'".');
                                        }';
            $returnJson['clean'] = true;     
           
        }else{
            
            $returnJson['error'] = true;       
            $returnJson['msg'] = 'Ошибка загрузки файла';       
            
        }    
            
        $this->putJSON($returnJson);
    }
    
    /// экспорт типа 
    public function exporttypeAction(){
        
        $typeId = $this->getParam('typeid');    
        
        $typeModel = new Admin_Model_Type;
        
        $typeRow = $typeModel->fetchRow(K_Db_Select::create()->where(array('type_id' => $typeId)));
             
        $typeRow = $typeRow->toArray();  
             
        $typeName = $typeRow['type_name'];
             
        $configArray = array('typeName' => $typeName);  
        
        $query = new K_Db_Query;       
        
        $typeTable = $query->q('SHOW CREATE TABLE type_'.$typeName.';'); 
       
         
        $zip = new ZipArchive;   
        
        //создаём папку если такой нет.
            
        if (!file_exists( ROOT_PATH.'/cache/typestmp/')) {
            
           mkdir( ROOT_PATH.'/cache/temp/', 0777, true);
           
        } 
          
         
        if ($zip->open(ROOT_PATH.'/www/upload/typestmp/'.$typeName.'.zip', ZipArchive::CREATE) === true){
                      
            // php файлы 
        	$zip->addFile(ROOT_PATH.'/application/type/model/'.$typeName.'.php', $typeName.'_model.php');/// Добавление модели типа
            $zip->addFile(ROOT_PATH.'/application/type/controller/'.$typeName.'.php', $typeName.'_controller.php');/// Добавление контроллера типа
            $zip->addFile(ROOT_PATH.'/application/admin/controller/gui/'.$typeName.'.php', $typeName.'_gui.php');/// Добавление GUI типа
            
            // иконка
            $zip->addFile(ROOT_PATH.'/www/adm/img/tree/'.$typeName.'.png', $typeName.'.png');/// Добавление иконки типа
                       
            // данные  
           	$zip->addFromString('config.json', json_encode($configArray)); // конфигурационный файл
            $zip->addFromString('typerow.json', json_encode($typeRow)); // строка типа из таблицы типов
            $zip->addFromString('typetable.sql', $typeTable[0]['Create Table']); // структура таблицы типа 
            
            $zip->close();
            
        }else{
            
        	echo 'Не могу создать архив!';
            
        };
       
        K_Request::redirect('/upload/typestmp/'.$typeName.'.zip');
    }
    
    /// создание нового типа
    public function createAction(){
        
        $this->disableRender = true;

        $form = new K_Form();
        
        $uploadDir = WWW_PATH . '/adm/img/tree';
        
        $allowedkeys = array(
            'type_name',
            'type_desc',
            'type_fields',
            'type_parent_elements',
            'type_children_elements',
            'ck_hmvc',
            'stype'
            );
            
        $values = array();

        $formData = $form->getData();

        $formPostArray = isset($_POST['type_fields']) ? $_POST['type_fields'] : false;

        parse_str(substr($_POST['type_fields'], 1), $formPostArray);
     
        if ($formPostArray != false) {
            
            K_Loader::load('formbuilder', APP_PATH . '/plugins');
            $formBuilder = new Formbuilder($formPostArray);
            $formArray = $formBuilder->get_encoded_form_array();
            
        } else {
            
            throw new Exception("Не заданы поля для нового типа!");
            
        }

        if (! $formData) {
            
            throw new Exception("Невозможно обработать форму без данных!");
           
        }

        $seo = false;

        foreach ($formData as $elementKey => $elementValue) {
            
            if (in_array($elementKey, $allowedkeys)) {
                if (is_string($elementValue)) {
                    $elementValue = trim($elementValue);
                }

                $values[$elementKey] = $elementValue;

                if ($elementKey == 'type_name') {
                    $values[$elementKey] = preg_replace("/[^a-z]/", "", $elementValue);

                    if (empty($values[$elementKey])) {
                        throw new Exception("Название нового типа не соответствует шаблону!");
                    }
                }

                if (($elementKey == 'type_fields') && (json_decode($formArray['form_structure']) == null)) {
                    if ((! is_null($formArray['form_structure'])) && (is_string($formArray['form_structure']) && $formArray['form_structure'] != 'null')) {
                        throw new Exception("Поля переданы не в json-формате! " . json_last_error());
                    }
                }

                if ($elementKey == 'type_parent_elements' || $elementKey == 'type_children_elements') {
                    if (is_array($elementValue)) {
                        if (in_array(1, $elementValue)) {
                            $elementValue = array('all');
                        }

                        if (in_array(2, $elementValue)) {
                            $elementValue = array();
                        }

                        if (! in_array(1, $elementValue) && ! in_array(2, $elementValue)) {
                            $elementValue = self::convertToSelectElements($elementValue, $elementKey);
                        }

                        $values[$elementKey] = $elementValue;
                    }
                }
                
                if (isset($_POST['seo']) && $_POST['seo']){                
                
                        $seo = true;
                
                }
                
            }
        }

        if ($form->hasFiles()) {
            
            $pathData = $form->moveUploadedFile('icon', $uploadDir, $form->getElement('type_name'));

            if ($pathData == null) {
                
                throw new Exception("Не удалось загрузить иконку!");
                
            }
            
        } else {
            
            throw new Exception("Не обнаружено иконки для типа!");
            
        }
        
        K_Tree_Types::add($values['type_name'], $values['type_desc'], $formArray['form_structure'], $values['type_children_elements'], $values['type_parent_elements'], 'type', true, $formArray, $values['ck_hmvc'], $seo);

        echo 'Новый тип успешно добавлен!';
        
    }

    public function updateAction() {
        
        $this->disableRender = true;

        $typesTable = new K_Tree_Types_Model();

        $typeId = $this->getParam('id');
        $types = $typesTable->select()->where('`type_id`=' . (int)$typeId)->fetchRow();
        $types = $types->toArray();

        $form = new K_Form();
        $allowedkeys = array(
            'type_desc',
            'type_fields',
            );
        $values = array();

        $formData = $form->getData();

        $formPostArray = isset($_POST['type_fields']) ? $_POST['type_fields'] : false;

        parse_str(substr($_POST['type_fields'], 1), $formPostArray);

        if ($formPostArray != false) {
            K_Loader::load('formbuilder', APP_PATH . '/plugins');
            $formBuilder = new Formbuilder($formPostArray);
            $formArray = $formBuilder->get_encoded_form_array();
        } else {
            throw new Exception("Не заданы поля для нового типа!");
        }

        if (! $formData) {
            throw new Exception("Невозможно обработать форму без данных!");
        }

        foreach ($formData as $elementKey => $elementValue) {
            if (in_array($elementKey, $allowedkeys)) {
                if (is_string($elementValue)) {
                    $elementValue = trim($elementValue);
                }

                $values[$elementKey] = $elementValue;

                if ($elementKey == 'type_fields' && json_decode($formArray['form_structure']) == null && ! empty($formArray['form_structure'])) {
                    throw new Exception("Поля переданы не в json-формате! " . json_last_error());
                }
            }
        }

        K_Tree_Types::update($types['type_name'], $values['type_desc'], $formArray['form_structure'], $formArray);

        echo 'Тип успешно обновлён!';
    }

    public function updateElementAction() {
        
        $this->disableRender = true;

        $values = array();

        $treeTable = new K_Tree_Model();

        $elementId = $this->getParam('id');

        $node = K_tree::getNode($elementId);

        $typeModelName = 'Type_Model_' . ucfirst($node['tree_type']);
        $typeModel = new $typeModelName();

        $query = new K_Db_Query();
        $columns = $query->q('SHOW COLUMNS FROM `type_' . $node['tree_type'] . '`');

        foreach ($columns as $c_key => $column) {
            $columns[$c_key] = $column->toArray();

            foreach ($_POST as $p_key => $value) {
                if ('type_' . $node['tree_type'] . '_' . $p_key == $columns[$c_key]['Field']) {
                    $values['type_' . $node['tree_type'] . '_' . $p_key] = $value;
                } elseif ($columns[$c_key]['Field'] == 'type_' . $node['tree_type'] . '_aaccess') {
                    $values['type_' . $node['tree_type'] . '_aaccess'] = '';
                }
            }
        }

        $form = new K_Form();
        $uploadDir = WWW_PATH . '/upload';
        $typeFiles = $form->getFiles();
        $typesModel = new K_Tree_Types_Model();
        $typeData = $typesModel->select('type_fields')->where('`type_name`="' . $node['tree_type'] . '"')->fetchRow();
        $typeData = $typeData->toArray();
        $typeData = unserialize($typeData['type_fields']);
        $typeData = json_decode($typeData['form_structure']);


        if ($form->hasFiles()) {
            $typeFiles = $form->getFiles();
        }


        foreach ($typeData as $key => $value) {
            if ($value->type == 'file') {

                if ($_POST[$value->values->name . '_delete']) {

                    $currentTypeData = $typeModel->select()->where('`type_' . $node['tree_type'] . '_id`=' . (int)$node['tree_id'])->fetchRow();
                    $currentTypeData = $currentTypeData->toArray();

                    $currentLastLoadedFile = $uploadDir . '/' . $currentTypeData['type_' . $node['tree_type'] . '_' . $value->values->name];

                    if (is_file($currentLastLoadedFile)) {
                        unlink($currentLastLoadedFile);
                    }
                    $values['type_' . $node['tree_type'] . '_' . $value->values->name] = '';

                } else
                    if ($form->hasFiles() && isset($typeFiles[$value->values->name])) {
                        if (is_dir($uploadDir)) {
                            $currentTypeData = $typeModel->select()->where('`type_' . $node['tree_type'] . '_id`=' . (int)$node['tree_id'])->fetchRow();
                            $currentTypeData = $currentTypeData->toArray();

                            $currentLastLoadedFile = $uploadDir . '/' . $currentTypeData['type_' . $node['tree_type'] . '_' . $value->values->name];

                            if (is_file($currentLastLoadedFile)) {
                                unlink($currentLastLoadedFile);
                            }
                          
                            $pathData = $form->moveUploadedFile($value->values->name, $uploadDir, md5(time() . rand()), true);
                            /*
                            if ($value->values->filter)
                            {
                            $filters = explode('|', $value->values->filter);
                            
                            foreach ($filters as $filter)
                            {
                            $filter = explode(':', $filter);
                            
                            if ($filter[0] == 'image')
                            {
                            $pathData['path'] = '/images/'.$pathData['filename'].'&'.$filter[1];
                            }
                            }
                            }*/

                        } else {
                           $this->putAjax('Директории для загрузки не существует "' . $value->values->name . '"!');
                        }
                        if ($pathData) {
                            $values['type_' . $node['tree_type'] . '_' . $value->values->name] = $pathData['filename'];
                        } else {
                            $this->putAjax('Не удалось загрузить файл "' . $value->values->name . '"!');
                        }

                    } elseif (($value->values->filter == 'required') && (! isset($typeFiles[$value->values->name]))) {
                        $this->putAjax('Файл "' . $value->values->name . '" не был загружен!');
                    } else {
                        if (! $values['type_' . $node['tree_type'] . '_' . $value->values->name]) {
                            unset($values['type_' . $node['tree_type'] . '_' . $value->values->name]);
                        }
                    }
            }

        }

        $typeModel->update($values, '`type_' . $node['tree_type'] . '_id`=' . (int)$elementId);

        echo 'Данные сохранены!
		<script type="text/javascript">
				setTimeout(function(){
					$("a.jstree-clicked").click();
				}
			});
		</script>
		';
    }

      //удаление типа файла
    private static function deleteFileTypeForm() {


    }

    public static function convertToSelectElements($selectArray, $elementName) {
        $formStructure = Gcontroller::loadFormStructure('/forms/add_type/');
        $structure = json_decode($formStructure['form_structure']);

        $result = array();

        for ($i = 0; $i < sizeof($structure); $i++) {
            if ($structure[$i]->values->name == $elementName) {
                foreach ((array )$structure[$i]->options as $optionKey => $option) {
                    if (in_array($optionKey, $selectArray)) {
                        $result[] = $option->value;
                    }
                }
            }
        }

        return $result;
    }

    public static function f_loadAllTypes($params = array()) {
        $result = array();

        $typesTable = new K_Tree_Types_Model();

        $types = $typesTable->select()->fetchArray();

        $result[0] = new stdClass();
        $result[0]->value = 'Все';
        $result[0]->baseline = 'undefined';

        $result[1] = new stdClass();
        $result[1]->value = 'Нет';
        $result[1]->baseline = 'undefined';

        for ($i = 0, $j = 2; $i < sizeof($types); $i++, $j++) {
            $result[$j] = new stdClass();
            $result[$j]->value = $types[$i]['type_name'];
            $result[$j]->baseline = 'undefined';
        }

        return $result;
    }
}
