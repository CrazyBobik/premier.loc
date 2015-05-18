<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Dev_Controller_DevTools extends Controller {

    public $helpers = array('form');
    public $formTemplate = array(
    
        'formStart' => '',
        'formEnd' => '<div style="margin: 0 auto; width: 90%; display: none; opacity: 0.0;" class="nNote nSuccess hideit" id="x_formsuccess_{{formid}}"><p></p></div>',
        'row' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight">{{element}}</div><div class="fix"></div></div>',
        'row_submit' => '{{element}}',
        'row_reset' => '{{element}}',
        'row_file' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'row_select' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'row_formbuilder' => '{{element}}'
        
        );

  public function onInit() {
	
        $this->formDictionary = new K_Dictionary();
        $this->formDictionary->loadFromIni( ROOT_PATH.'/configs/forms/errors.txt' );
        K_Validator::setDefaultDictionary( $this->formDictionary );
		   		
  }   
    
    /// Инструменты разработчика
  public function indexAction(){
        
        $query = new Query;
        
        $this->view->title = 'Инструменты разработчика';
        $this->view->header = 'Инструменты разработчика';
        
        $this->view->headers = array(array('title' => 'Менеджер типов',
                                           'href' => '/admin/typesmanager/'
                                          ),
                                     array('title' => 'Инструменты разработчика',
                                          )
                                    );                   

        $this->view->formStructure = Gcontroller::loadFormStructure('/forms/add_type/', get_class());
         
        $this->view->types = $query->q('SELECT type_name FROM types ORDER BY type_name' );  
            
        $this->view->actionType = 'create';

        $this->render('devtools');
        
    }
 
    /// Форма добавления новой модели
  public function addModelAction(){
    
        $this->disableLayout = true;
        $this->render('addmodel');
        
  }
  
    /// Форма добавления нового контроллера
  public function addControllerAction(){
    
        $this->disableLayout = true;
        $this->render('addcontroller');
       
  } 
  
    /// Форма добавления нового HMVC блока
  public function addHMVCAction(){
    
        $this->disableLayout = true;
        $this->render('addHMVCblock');
        
  } 
      
    /// Сохранение текста сконфигурированной модели 
  public function saveModelAction(){
    
        $name = $_POST['name']; 
        $table = $_POST['table']; 
        $primary_key = $_POST['primary_key'];
                 
        $modelPath = APP_PATH."/admin/model/".$name.'.php'; //путь сохранения модели.
         
        $modelText = "<?php";
        $modelText.= "defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');"."\n";
        $modelText.= "\r"."\r"."class Dev_Model_".ucfirst($name)." extends Model{"."\n";
        $modelText.= "\r"."\r"."\r"."\r".'var $name = "'.$table.'";'."\n";
        $modelText.= "\r"."\r"."\r"."\r".'var $primary = "'.$primary_key.'";'."\n";
        $modelText.= "\r"."\r"."}?>"."\n";
        
        file_put_contents($modelPath, $modelText);
        
        if(!filesize($modelPath)>100){
            
               $returnJson['error'] = true;  
               $returnJson['msg'] = "Ошибка добавления модели";
               
               $this->putJSON($returnJson);
            
        }else{
        
               $returnJson['error'] = false;  
               $returnJson['msg'] = "Модель удачно добавлена";
               
               $this->putJSON($returnJson);
               
        }
  }
  
    /// Сохранение текста сконфигурированного контроллера
  public function saveControllerAction(){
    
        $module = $_POST['module']; // название модуля админский, пользовательский
        
        $name = $_POST['name']; // название контроллера
             
        $controllerPath = APP_PATH."/$module/controller/".$name.'.php'; //путь сохранения контроллера.
        $controllerText = "<?php"."\n";
        $controllerText.= "defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');"."\n";
        $controllerText.= "\r"."\r"."class Dev_Model_".ucfirst($name)." extends Model{"."\n";
        $controllerText.= "\r"."\r"."\r"."\r".'var $name = "'.$table.'";'."\n";
        $controllerText.= "\r"."\r"."\r"."\r".'var $primary = "'.$primary_key.'";'."\n";
        $controllerText.= "\r"."\r"."}?>"."\n";
        
        file_put_contents($controllerPath, $controllerText);
        
        if(!filesize($controllerPath)>100){
            
               $returnJson['error'] = true;  
               $returnJson['msg'] = "Ошибка добавления контроллера";
               $this->putJSON($returnJson);
            
        }else{
        
               $returnJson['error'] = false;  
               $returnJson['msg'] = "Контроллер удачно добавлен";
               $this->putJSON($returnJson);
               
        }
  }
  
    /// Сохранение текста сконфигурированного HMVC блока
  public function saveHMVCAction(){
    
        $name = $_POST['name']; // название блока
         
        $controllerStr = '<?php defined(\'K_PATH\') or die(\'DIRECT ACCESS IS NOT ALLOWED\')'."\n";
        $controllerStr.= "class Blocks_Controller_".ucfirst($_POST['name'])." extends Controller {"."\n";
        $controllerStr.= 'public $helpers = array();'."\n";
        $controllerStr.= "public function indexAction() {"."\n";
        $controllerStr.= '$this->view->meta = $this->getParam(\'meta\');'."\n";
        $controllerStr.= '$this->view->node = $this->getParam(\'node\'); // информация о самой ноде'."\n";
        $controllerStr.= '$this->view->own = $this->getParam(\'own\');'."\n";
        $controllerStr.= '}'."\n";
        $controllerStr.="\r"."\r"."}?>"."\n";
              
        $controllerPath = APP_PATH."/application/blocks/controller/".$name.'.php'; //путь сохранения контроллера HMVC блока
         
        $templateDirPath = APP_PATH."/application/blocks/templates/".$name; //путь сохранения шаблона HMVC блока 
                
        $templateNamePath = APP_PATH."/application/blocks/templates/".$name.'/'.$name.'.php'; //путь сохранения шаблона HMVC блока    
      
        mkdir($templateDirPath, 0777);
          
        file_put_contents($controllerPath, $controllerStr); 
               
        file_put_contents($templateNamePath, $templateStr);
         
        if(!filesize($templateNamePath)>100){
            
               $returnJson['error'] = true;  
               $returnJson['msg'] = "Ошибка добавления шаблона HMVC блока";
               
               $this->putJSON($returnJson);
               $error = true;
               
        }
        
        if(!filesize($controllerPath)>100){
            
               $returnJson['error'] = true;  
               $returnJson['msg'] = "Ошибка добавления контроллера HMVC блока";
               
               $this->putJSON($returnJson);
               $error = true;
               
        }
        
        $returnJson['error'] = false;  
        $returnJson['msg'] = "Ошибка добавления контроллера HMVC блока";
           
        $this->putJSON($returnJson);
  }
  
}
