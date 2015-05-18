<?php

class Dev_Controller_AddController extends K_Controller_Dev {
   
    /// Инструменты разработчика
  public function indexAction(){
           
        $this->view->modules = DevConfig::$modules;
        $this->view->controllers = DevConfig::$controllers;
        
        $this->render('addcontroller');
        
    }
 
  /// Сохранение текста сконфигурированного контроллера
  public function saveAction(){
    
        $module = $_POST['module']; // название модуля админский, пользовательский
        
        $controller = $_POST['controller']; // название контроллера
             
        $path = APP_PATH."/$module/controller/".$controller.'.php'; //путь сохранения контроллера.
        
        $lables = array( 
                   'controller'=>'Название контроллера'
        );
        
        $data =array('controller' => K_string::treat($_POST['controller'], 33));
        
        $validate = array( 
        
          'controller' => array( 'required' => true,
                                 'notEmpty',
                                 'minlen'=>'3',
                                 'maxlen'=>'32'
                            )
         );
        
        $model = new Dev_Model_Valid;
        
        if($model->isValidRow($data, $validate)){
        
            if(file_exists($path)){
                
                  $returnJson['error'] = true;  
                  $returnJson['msg'] = "Такой контроллер уже существует";
                  $returnJson['msgid'] ='#flash-msg-nNote';
                  
                  $this->putJSON($returnJson);
                
            }
            
            // устанавливаем теги и рендерим
            $tags = array(    'module'=>ucfirst($module),
                              'controllerName'=> ucfirst($controller),
                              'templateName'=> $controller
                          );
            
            $render = new K_TemplateRender(null,  null, $left = '<%', $right = '%>' );
          
            $render->setTags($tags);
               
            $render->fromFile(APP_PATH.'/dev/phptemplates/mvc/controller.ptpl');
            
            $code = $render->assemble();
            
            // записываем
            file_put_contents($path, $code);
            
            if(!filesize($path)>100){
                
                   $returnJson['error'] = true;  
                   $returnJson['msg'] = "Ошибка добавления контроллера";
                   $this->putJSON($returnJson);
                
            }else{
                   
               if(!empty($_POST['addview'])){    
                   if(!K_file::create(APP_PATH.'/'.$module.'/view/'.$controller.'/'.$controller.'.phtml', true)){
                        
                       $returnJson['error'] = true;  
                       $returnJson['msg'] = "Ошибка создания представления";
                       $this->putJSON($returnJson);
                       
                   }
               }
                        
               $returnJson['error'] = false;
                 
               if( !empty($_POST['addview'])){
                
                  $returnJson['msg'] = "Контроллер и представление удачно созданы";
               }
               else{
                
                  $returnJson['msg'] = "Контроллер удачно создан";
              }
              
               $returnJson['form'] = 'clean';
               $this->putJSON($returnJson);
                   
            }
            
            
        }else{
            
             $returnJson['error'] = true;
             $returnJson['errormsg'] = $model->getErrorsD($lables);
             $this->putJSON($returnJson);
        }
        
        
  }
  
}
