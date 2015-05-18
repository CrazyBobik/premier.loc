<?php

class Dev_Controller_AddModel extends K_Controller_Dev {
   
    /// Инструменты разработчика
  public function indexAction(){
           
        $this->view->modules = DevConfig::$modules;
        $this->view->controllers = DevConfig::$controllers;
   
        $this->view->tables = K_q::query("SELECT
                                              st.TABLE_NAME `table`,
                                              st.COLUMN_NAME `primary`
                                         FROM
                                              information_schema.STATISTICS st
                                         WHERE
                                              st.TABLE_SCHEMA = '".AllConfig::$mysqBDConf['database']."' and
                                              st.INDEX_NAME = 'PRIMARY'
                                        ");
        
        $this->render('addmodel');
        
    }
  
  /// Сохранение текста сконфигурированной модели
  public function saveAction(){
    
        $module = $_POST['module']; // название модуля админский, пользовательский
        
        $model = $_POST['model']; // название модуля админский, пользовательский
        
        $table = $_POST['table']; // название контроллера
        
        $primary = $_POST['primary']; // название контроллера
             
      
        
        $lables = array(
                   'module'=>'Для модуля',
                   'model'=>'Название модели',
                   'table'=>'Название таблицы',
                   'primary'=>'Первичный ключ'
        );
        
        $data = array( 'module' => K_string::treat($_POST['module'], 33),
                       'model' => K_string::treat($_POST['model'], 33),
                       'table' => K_string::treat($_POST['table'], 33),
                       'primary' => K_string::treat($_POST['primary'], 11)
                     );
        
        $validate = array( 
        
          'module' => array('required' => true,
                            'notEmpty',
                            'minlen'=>'2',
                            'maxlen'=>'64'
                           ),
          'model' => array( 'required' => true,
                            'notEmpty',
                            'minlen'=>'2',
                            'maxlen'=>'64',
                           ),
          'table' => array( 'required' => true,
                            'notEmpty',
                            'minlen'=>'2',
                            'maxlen'=>'64'
                           ),
          'primary' => array('required' => true,
                             'notEmpty',
                             'maxlen'=>'64'
                             )
         );
        
        $model = new Dev_Model_Valid;
        
        if($model->isValidRow($data, $validate)){
            
            $path = APP_PATH.'/'.$data['module'].'/model/'.$data['model'].'.php'; //путь сохранения контроллера.
            
            if(file_exists($path)){
                
                  $returnJson['error'] = true;  
                  $returnJson['msg'] = "Такая модель для данного модуля уже существует";
                  $returnJson['msgid'] ='#flash-msg-nNote';
                  
                  $this->putJSON($returnJson);
                
            }
            
            // устанавливаем теги и рендерим
            $tags = array(    'module'=>ucfirst($data['module']),
                              'model'=> ucfirst($data['model']),
                              'table'=> $data['table'],
                              'primary'=> $data['primary'],
                              'methods'=> '', //Todo добавить конфигуратор методов
                          );
            
            $render = new K_TemplateRender(null,  null, $left = '<%', $right = '%>' );
          
            $render->setTags($tags);
               
            $render->fromFile(APP_PATH.'/dev/phptemplates/mvc/model.ptpl');
            
            $code = $render->assemble();
            
            // записываем
            file_put_contents($path, $code);
            
            if(!filesize($path)>100){
                
                  $returnJson['error'] = true;  
                  $returnJson['msg'] = "Ошибка добавления модели";
                  $this->putJSON($returnJson);
                
            }else{
                        
                  $returnJson['error'] = false;
                  $returnJson['msg'] = "Модель удачно создана";
                
                  $this->putJSON($returnJson);
                   
            }
            
        }else{
            
             $returnJson['error'] = true;
             $returnJson['errormsg'] = $model->getErrorsD($lables);
             $this->putJSON($returnJson);
        }
      
  }
  
}
