<?php

class Dev_Controller_AddHmvcBlock extends K_Controller_Dev {
   
    /// Инструменты разработчика
  public function indexAction(){
           
        $this->view->modules = DevConfig::$modules;
        $this->view->controllers = DevConfig::$controllers;
        
        $this->render('addhmvcblock');
        
    }
 
  /// Сохранение текста сконфигурированного контроллера
  public function saveAction(){
       
	    $lables = array( 
	   			   'title'=>'Заголовок HMVC блока',
                   'name'=>'Название HMVC блока в системе(на английском)'
        );
        
        $data =array('name' => K_string::treat($_POST['name'], 33),
				   	 'title' => K_string::treat($_POST['title'], 33));
        
        $validate = array( 
		
            'title' => array( 'required' => true,
                              'notEmpty',
                              'minlen'=>'3',
                              'maxlen'=>'32'
                            ),
			
            'name' => array( 'required' => true,
                             'notEmpty',
                             'minlen'=>'3',
                             'maxlen'=>'32'
                            )
        );
        
        $model = new Dev_Model_Valid;
        
        if($model->isValidRow($data, $validate)){
			$module="blocks";
			
            $pathController = APP_PATH."/$module/controller/".$data['name'].'.php'; //путь сохранения контроллера.
         			 
			if(file_exists($pathController)){
				
				$returnJson['error'] = true;  
				$returnJson['msg'] = "Такой HVMC блок уже существует";
				$returnJson['msgid'] ='#flash-msg-nNote';
				  
				$this->putJSON($returnJson);
				
			}
            
            // устанавливаем теги и рендерим
						
            $tags = array(    'module'=>ucfirst($module),
                              'controllerName'=> ucfirst($data['name']),
                              'templateName'=> $data['name']
                          );
            
            $render = new K_TemplateRender(null,  null, $left = '<%', $right = '%>' );
          
            $render->setTags($tags);
               
            $render->fromFile(APP_PATH.'/dev/phptemplates/mvc/controller.ptpl');
            
            $code = $render->assemble();
            
            // записываем
            file_put_contents($pathController, $code);
            
            if(!filesize($pathController)>100){
                
                    $returnJson['error'] = true;  
                    $returnJson['msg'] = "Ошибка добавления контроллера";
                    $this->putJSON($returnJson);
                
            }else{
              
                    if(!K_file::create(APP_PATH.'/'.$module.'/view/'.$data['name'].'/'.$data['name'].'.phtml', true)){
                        
                       $returnJson['error'] = true;  
                       $returnJson['msg'] = "Ошибка создания представления";
                       $this->putJSON($returnJson);
                       
                    }
				   
            }
			
		    $hmvcTitle = $data['title'];
		    $hmvcName = K_String::urlClear($data['name']);
			
			$nodeM = new Type_Model_Node;					
			$arrRegion = K_CupTree::addNode($nodeM, 'node', '/hmvcblocks/', $hmvcTitle , $hmvcName, array() , $errSql = false, true);
                    					
		    $returnJson['error'] = false;
		    $returnJson['msg'] = "Контроллер удачно создан";
		    $returnJson['form'] = 'clean';
		    $this->putJSON($returnJson);
                 
        }else{
            
            $returnJson['error'] = true;
            $returnJson['errormsg'] = $model->getErrorsD($lables);
            $this->putJSON($returnJson);
			 
        }
 	}
	
}
