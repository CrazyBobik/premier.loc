<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Dev_Controller_EditTypes extends Controller {

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
            
            
            /*  если необходимо добавлять текущее время к дате 
            if($value->values->name  == 'date'){  
                $dates =  K_date::dateParse($_POST['date']);
                $values['type_' . $node['tree_type'] . '_date'] = date('Y-m-d',$dates['ts']) .' '.date('G:i:s',time()); 
            }           */  // var_dump($typeFiles);
            
            if ($value->type == 'file' || $value->type == 'multifile' ) {
              
               if($value->type == 'multifile'){
                
                $currentTypeData = $typeModel->select()->where('`type_' . $node['tree_type'] . '_id`=' . (int)$node['tree_id'])->fetchRow();
                $currentTypeData = $currentTypeData->toArray();
                   // массив файлов сохранённый в базе;
                  if ($filesArray = unserialize($currentTypeData['type_' . $node['tree_type'] . '_' . $value->values->name])){
                        $i=0;  
                        $fcount=count($filesArray);
                    for($i=0; $i<$fcount; $i++){
                        $isDelete = false;
                          if ($_POST[$value->values->name . '_delete_'.$i] || $typeFiles[$value->values->name.'_f_'.$i]){
                             $currentLastLoadedFile=$uploadDir . '/' .$filesArray[$i]['f'];
                             if (is_file($currentLastLoadedFile)) {
                                 unlink($currentLastLoadedFile);
                             }
                             unset($filesArray[$i]);
                             $isDelete = true;
                          };
                 
                          if (!$isDelete && $_POST[$value->values->name.'_t_'.$i]){
                             $filesArray[$i]['t'] = $_POST[$value->values->name.'_t_'.$i];
                          };
                    }
                    
                  } 
                  
                   foreach($typeFiles as $k => $v){ 
                 
                     if (preg_match('/^'.$value->values->name.'_f_([0-9]*)$/s',$k,$m)){
                 
                     if (is_dir($uploadDir)) {
                            $pathData = $form->moveUploadedFile($k, $uploadDir, md5(time() . rand()), true);
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
                           $this->putAjax('Директории для загрузки не существует "' . $k . '"!');
                        }
                        
                        if ($pathData) {
                            
                            $fileOne['f'] = $pathData['filename'];
                            
                            if(!empty($_POST[$value->values->name . '_t_'.$m[1]])){
                                
                              $fileName = $_POST[$value->values->name . '_t_'.$m[1]];
                              
                            }else{
                                
                              $fileName = $v['name']; 
                              
                            }
                            
                            $fileOne['t'] = $fileName;
                            
                            $filesArray[] = $fileOne;
                            
                        } else {
                            $this->putAjax('Не удалось загрузить файл "' . $k . '"!');
                        }
                   } 
                }
                   
                //упорядочим массив
                $filesArrayOrdered=array();
                foreach($filesArray as $v){
                    if (isset($v)){
                         $filesArrayOrdered[] = $v;   
                    }
                }
                
               $values['type_' . $node['tree_type'] . '_' . $value->values->name] = count($filesArrayOrdered)? serialize($filesArrayOrdered) : '' ; 
                
               }else
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

                    } else if (($value->values->filter == 'required') && (! isset($typeFiles[$value->values->name]))) {
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
			$(document).ready(function(){
				setTimeout(function(){
				loadTabs('.(int)$elementId.');
				}, 2000);
			});
		</script>
		';
    }

    

  
}
