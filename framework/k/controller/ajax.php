<?php 

class K_Controller_Ajax extends K_Controller {

	public function onInit(){
        
        $this->disableRender = true;
        $this->formDictionary = new K_Dictionary();
        $this->formDictionary->loadFromIni(ROOT_PATH . '/configs/forms/errors.txt');
        
        K_Validator::setDefaultDictionary($this->formDictionary);
        
    }
	
	// запрещён индекс актион
    public function indexAction(){
        
        $this->putAjax('ERROR');
       
    }    
    
    public function isAjaxErr(){
        if (!K_Request::isAjax()) {
                 $this->putAjax('ERROR');
        }; 
    }
    
     public function isPostErr(){
         if (! K_Request::isPost()){
                    $this->putAjax('ERROR');
         }
    }

}

?>