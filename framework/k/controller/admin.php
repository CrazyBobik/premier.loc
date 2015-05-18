<?php 

class K_Controller_Admin extends K_Controller {
    
      protected function onInit(){
        
        foreach(AdminConfig::$menuTabs as $v){
            
              if(in_array($this->getOption('controller'), $v['controllers'])){ 
                
                    $this->view->menuTabs = $v['menuTabs'];
                
              }
            
        } 
        
        if(isset(AdminConfig::$crudTables[$this->getOption('controller')])){
        
            $this->view->crudTable = AdminConfig::$crudTables[$this->getOption('controller')];  
            $this->crudConfig = K_Config::load($this->view->crudTable);
        
        }
        
        $this->view->activeTab = $this->getOption('controller');  
        $this->view->bigtable = true;
        
    }
 
}

?>