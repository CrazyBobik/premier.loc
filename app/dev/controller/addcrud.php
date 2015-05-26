<?php

class Dev_Controller_AddCrud extends K_Controller_Dev {
   
    /// Инструменты разработчика
    public function indexAction(){
           
        $this->view->modules = DevConfig::$modules;
        $this->view->controllers = DevConfig::$controllers;
        
        $dirArr = K_File::rdir(BUILDCONFIGS_PATH.'/cruds/');
        
        foreach($dirArr as $v){
            
            require_once BUILDCONFIGS_PATH.'/cruds/'.$v;
             
            $parray = pathinfo($v);
                            
            $this->view->cruds[$parray['filename']] = $crudConfig['title'];
             
        }
              
        $this->render('addcrud');
        
    }
 
    /// Сохранение текста сконфигурированного контроллера
    public function generateAction(){
             
        require_once BUILDCONFIGS_PATH.'/cruds/'.$_POST['name'].'.php';        
      
        $module = 'admin';
        
        $controllerPath = APP_PATH.'/'.$module.'/controller/'.$crudConfig['name'].'.php'; //путь сохранения контроллера.
        
        /*
        if(file_exists($controllerPath)){
            
              $returnJson['error'] = true;  
              $returnJson['msg'] = "Контроллер создаваемого CRUD уже существует";
              $returnJson['msgid'] ='#flash-msg-nNote';
              
              $this->putJSON($returnJson);
            
        }
        */
        
        $controllerTags = array(  
        
            'title'=>ucfirst( $crudConfig['title']),
            'controllerName'=>ucfirst( $crudConfig['name']),
            'controller'=>$crudConfig['name'],
                        
            'model'=>$crudConfig['model'],
                       
            'table'=>$crudConfig['table'],
                          
            'template'=>$crudConfig['name']
                          
        );
                     
        // поля для запроса 
        foreach($crudConfig['fields'] as $k=>$v){
            
            $fields[] = k_q::qk((isset($v['alias'])? $v['alias'] : $crudConfig['alias'])).'.'.k_q::qk((isset($v['field'])? $v['field'] : $k)).' '.k_q::qk($k);

            $fieldsEdit[] = k_q::qk($crudConfig['alias']).'.'.k_q::qk($k).' '.k_q::qk($k);
           
            // шаблоны при передачи данных в рендер.
            if(isset($v['width']) && $v['width']>0){
                				
                if( $v['template']){
                    
                    $itemArray[] = '$itemRow["'.$k.'"] = !empty($v["'.$k.'"])? \''. K_TemplateRender::rendString( $v['template'], array('value'=>'$v["'.$k.'"]')).'\' :""'; 
                
                }elseif($v['crop']){
                    
                    $itemArray[] = '$itemRow["'.$k.'"] = k_string::trunc(htmlspecialchars($v["'.$k.'"]),'.$v['crop'].',true)';
                   
                }else{
				
                    $itemArray[] = '$itemRow["'.$k.'"] = strip_tags(htmlspecialchars($v["'.$k.'"]))';
                }
                
            }
                 
        }
        
        $controllerTags['itemArray'] = '$itemRow=array()'.";\n".implode(";\n", $itemArray).";\n";
       
        $loadQueryTags['table'] = $crudConfig['table'];
                      
        $loadQueryTags['fields'] = implode(',', $fields);
        
        $loadQueryTags['alias'] = $crudConfig['alias'];
           
        $controllerTags['loadQuery'] = K_TemplateRender::rendString($crudConfig['loadQuery'], $loadQueryTags);
        
        $editQueryTags['prefixid'] = $crudConfig['alias'].'.'.$crudConfig['primary'];
        $editQueryTags['fields'] = implode(',', $fieldsEdit);
        
        $editQueryTags['alias'] = $crudConfig['alias'];
        $editQueryTags['table'] = $crudConfig['table'];
        
        $controllerTags['editQuery'] = K_TemplateRender::rendString($crudConfig['editQuery'], $editQueryTags);
           
        $fieldClass = $crudConfig['name'].'-field';
                  
        $tags = array(
                                                                  
            'tableWarapperId'=> $crudConfig['name'].'_table_wrapper',
            'crudWarapperId'=>  $crudConfig['name'].'_crud_wrapper',
            'crudPaginateId'=> $crudConfig['name'].'_paginate',
            'controller'=> $crudConfig['name'],
            'fieldClass'=> $fieldClass
                          
        );
                    
        $fieldClass = $crudConfig['name'].'-field';
                    
        $rowRender = '\'<tr class="'.$crudConfig['name'].'_row" id="'.$crudConfig['name'].'-row-\' + v.'.$crudConfig['primary'].' + \'" rel="\' + v.'.$crudConfig['primary'].'name + \'" >';
                    
        foreach($crudConfig['fields'] as $k => $v){
                          
            if(!empty($v['width'])){
						  
                $rowRender.="<td>' + v.".$k." + '</td>" ;
						   
                $sWidth = 'style="width:'.$v['width'].'px;"';
							
                $tags['header'].= '<th class="ui-state-default" rowspan="1" colspan="1" '.$sWidth.'>'."\n";
                $tags['header'].= '<div class="DataTables_sort_wrapper">'.$v['lable'].'<br/>'."\n";
				  
                if($v['set']=='like' || $v['set']=='add'){
								
                    if(isset($v['otions'])||isset($v['select'])){
										
                        $tags['header'].= '<select type="text" name="'.$k.'" id="'.$crudConfig['name'].'_'.$k.'" '.$sWidth.'  class="'.$fieldClass.'"  >'."\n";
										
                        $tags['header'].= '<?=K_jformhelper::options($this->selects->'.$k.', isset($_GET[\''.$k.'\'])? strip_tags(htmlspecialchars($_GET["'.$k.'"])) : null)?>';
													
                        $tags['header'].= '</select>'."\n";
												   
                    }
                    else{
										
                        $tags['header'].= '<input type="text" name="'.$k.'" <?=isset($_GET[\''.$k.'\'])? \'value="\'.strip_tags(htmlspecialchars($_GET["'.$k.'"])).\'"\' : ""?>  id="'.$crudConfig['name'].'_'.$k.'" '.$sWidth.' class="'.$fieldClass.'" />'."\n";
										
                    }
								  
                }elseif($v['set']=='between'){
								
                    if ($v['type']=="TIMESTAMP"){
									
                        $datepicker='datepicker';
									
                    }
								 
                    $sWidth = 'style="width:'.($v['width']-20).'px;"';
								  
                    $tags['header'].= 'от<input '.$class.' type="text" name="'.'start-'.$k.'" id="'.'start-'.$crudConfig['name'].'_'.$k.'" value="" '.$sWidth.' class="'.$fieldClass.' '.$datepicker.'"  />'.'<br/>'."\n";
                    $tags['header'].= 'до<input '.$class.' type="text" name="'.'stop-'.$k.'" id="'.'stop-'.$crudConfig['name'].'_'.$k.'" value="" '.$sWidth.' class="'.$fieldClass.' '.$datepicker.'"  />'."\n";
								
                }
							 
                $tags['header'].= '</div>'."\n";
                $tags['header'].= '</th>'."\n";
							
                // поля для редактирования
							  
                if(isset($v['otions'])){
								
                    $selectsOne = '$res = k_q::query("SELECT * FROM '.$v['otions']['table'].'");'."\n";
										
                    $selectsOne .= 'foreach( $res as $t){'."\n";
														
                    $selectsOne .= '$options[$t[\''.$v['otions']['title'].'\']] = array(\'value\'=>$t[\''.$v['otions']['value'].'\']);'."\n";
													
                    $selectsOne .= "};"."\n";
													
                    $selectsOne .= '$this->view->selects->'.$k.' = $options;'."\n";
												  
                    $selectsEdit .= '$options = array();'."\n".$selectsOne."\n";
												  
                    $selects .= '$options = array(\'Любой\'=>array(\'value\'=>\'\'));'."\n".$selectsOne."\n";
												  
                }if(isset($v['select'])){
								
                    $selectsOne .= '$fieldConfig = $this->crudConfig->fieldConfig("'.$k.'")'."\n";
								
                    $selectsOne .= 'foreach( $fieldConfig["select"] as $m=>$t){'."\n";
														
                    $selectsOne .= '$options[$t] = array(\'value\'=>$m);'."\n";
													
                    $selectsOne .= "};"."\n";
													
                    $selectsOne .= '$this->view->selects->'.$k.' = $options;'."\n";
												  
                    $selectsEdit .= '$options = array();'."\n".$selectsOne."\n";
												  
                    $selects .= '$options = array(\'Любой\'=>array(\'value\'=>\'\'));'."\n".$selectsOne."\n";
                }
            }
                          
            // формирования полей для редактирования
            if($k!=$crudConfig['primary']){
                            
                $editFields .= '<tr>
                                            			<td>
                                            	           	'.$v['lable'].'
                                            			</td>
                                            			<td>'."\n";
                                                       
                if(isset($v['otions'])|| isset($v['select'])){
                                                                    
                    $editFields.= '<select type="text" name="'.$k.'" id="'.$crudConfig['name'].'_'.$k.'"  class="'.$fieldClass.'"  >'."\n";
                                                                    
                    $editFields.= '<?=K_jformhelper::options($this->selects->'.$k.', $this->item["'.$k.'"])?>';
                                                                                
                    $editFields.= '</select>'."\n";
                                                                               
                }elseif($v['type']=='text'){
                                                            
                    $editFields.= '<textarea name="'.$k.'" rows="10" cols="50" id="'.$crudConfig['name'].'_'.$k.'" class="'.$fieldClass.'"><?=strip_tags(htmlspecialchars($this->item["'.$k.'"]))?></textarea>';
                                                             
                }else{
                                                                    
                    if ($v['type']=="TIMESTAMP"){
                                                                    
                        $datepicker='datepicker';
                                                                    
                    }else{
																	
                        $datepicker='';
																	
                    }
                                                                   
                    $editFields.= '<input type="text" name="'.$k.'" id="'.$crudConfig['name'].'_'.$k.'" value="<?=strip_tags(htmlspecialchars($this->item["'.$k.'"]))?>" class="'.$fieldClass.' '.$datepicker.'" />'."\n";
                                                                    
                }
                                                  
                $editFields .= '</td></tr>';
            }
                        
        }
                    
        $rowRender .= '<td><a href="javascript:void(0);" data-id="\' + v.'.$crudConfig['primary'].' + \' "class="crud-edit"></a><a title="Удалить запись" href="javascript:void(0);" data-id="\' + v.'.$crudConfig['primary'].' + \'" class="crud-delete"></a></td></tr>\'';

        $tags['rowRender'] = $rowRender;
					
        $controllerTags['crudName'] = $crudConfig['name'];
        $controllerTags['selects'] = $selects;
        $controllerTags['selectsEdit'] = $selectsEdit;
        $controllerTags['tableWarapperId'] =  $crudConfig['name'].'_table_wrapper';
                   
        $controllerCode = K_TemplateRender::rendFile(APP_PATH.'/dev/phptemplates/crud/controller.ptpl', $controllerTags);
            
        // записываем
        file_put_contents($controllerPath, $controllerCode);
                   
        if(!filesize($controllerPath)>100){
                        
            $returnJson['error'] = true;
            $returnJson['msg'] = "Ошибка добавления контроллера";
            $this->putJSON($returnJson);
                        
        }
              
        $viewCode = K_TemplateRender::rendFile(APP_PATH.'/dev/phptemplates/crud/view/crud.ptpl', $tags);
                    
        $viewPath = APP_PATH.'/'.$module.'/view/'.$crudConfig['name'].'/'.$crudConfig['name'].'.phtml';
                   
        // записываем
        K_file::create($viewPath, true);
        file_put_contents($viewPath, $viewCode);
                    
        if(!filesize($viewPath)>100){
                        
            $returnJson['error'] = true;
            $returnJson['msg'] = "Ошибка добавления контроллера списка";
            $this->putJSON($returnJson);
                        
        }
                    
        $viewEditTags['fields']= $editFields;
        $viewEditTags['primary']= $crudConfig['primary'];
        $viewEditTags['controller']= $crudConfig['name'];
                   
        $viewEditCode = K_TemplateRender::rendFile(APP_PATH.'/dev/phptemplates/crud/view/edit.ptpl', $viewEditTags);
                  
        $viewEditPath = APP_PATH.'/'.$module.'/view/'.$crudConfig['name'].'/edit.phtml';
                    
        // записываем
        K_file::create($viewEditPath, true);
        file_put_contents($viewEditPath, $viewEditCode);
                                     
        if(!filesize($viewEditPath)>100){
                        
            $returnJson['error'] = true;
            $returnJson['msg'] = "Ошибка создания файла представления редактирования";
            $this->putJSON($returnJson);
                        
        }
                  
        require_once CONFIGS_PATH.'/admin_cruds.php';
        $crudTables[$crudConfig['name']] = $crudConfig;
        file_put_contents(CONFIGS_PATH.'/admin_cruds.php', '<?php'."\n".'$crudTables = '.var_export($crudTables, true).';');
                   
        $returnJson['error'] = false;
        $returnJson['msg'] = "CRUD удачно создан";
        $this->putJSON($returnJson);
           
        /*
        }else{
            
             $returnJson['error'] = true;
             $returnJson['errormsg'] = $model->getErrorsD($lables);
             $this->putJSON($returnJson);
        }*/
        
    }
  
    /// загрузка конфига
    public function editAction(){
    
        $this->disableLayout = true;
         
        if(isset($_POST['name']) && !empty($_POST['name'])){
        
            $pArray = pathinfo($_POST['name']);
            // var_dump();
            require_once BUILDCONFIGS_PATH.'/cruds/'.$pArray['filename'].'.php'; 
            
        }
                   
        $this->view->crud = $crudConfig;
        
        $this->render('edit');
        
    }
  
    /// Сохранение сконфигурированного контроллера
    public function saveAction(){
    
        $config = array('name'=>$_POST['name'],
            'title'=>$_POST['title'],
            'alias'=>$_POST['alias'],
            'primary'=>$_POST['primary'],
            'model'=>$_POST['model'],
            'nafieldsMaxLenme'=>$_POST['fieldsMaxLen'],
            'loadQuery'=>$_POST['loadQuery'],
            'editQuery'=>$_POST['editQuery'],
        );
        
        $c = count($_POST['fieldkey']);
         
        $fieldKeys = array('set','alias','field','template');
              
        for($i=0; $i<$c; $i++){
        
            $field=array();
        
            $field['lable'] = $_POST['lable'][$i];
            $field['type'] = $_POST['type'][$i];
       
            foreach( $fieldKeys as $v){
        
                if(isset($_POST[$v][$i])&&!empty($_POST[$v][$i])){
            
                    $field[$v] = $_POST[$v][$i];
          
                }
         
            }
         
            $config['fields'][$_POST['fieldkey'][$i]] = $field;
        
        }
       
          
        $returnJson['error'] = false;
        $returnJson['msg'] = "CRUD удачно сохранён";
      
        $this->putJSON($returnJson);
    }

}
