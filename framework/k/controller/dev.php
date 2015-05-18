<?php 

class K_Controller_Dev extends K_Controller {
    
      protected function onInit(){
        
        foreach(DevConfig::$menuTabs as $v){
            
              if(in_array($this->getOption('controller'), $v['controllers'])){ 
                
                    $this->view->menuTabs = $v['menuTabs'];
                
              }
            
        } 
        
        $this->view->crudTable = DevConfig::$crudTables[$this->getOption('controller')];  
        
        $this->view->activeTab = $this->getOption('controller');  
            
        $this->view->bigtable = true;
        
    }
 
}

?>