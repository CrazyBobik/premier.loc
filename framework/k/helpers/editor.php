<?php 
class editorHelper {
	   
   protected $editAccess = false; 
   private $init = false; 
   /*
   public function initCheack() {
      
  	   }*/   
       
   public function data($nodeId, $accessRes ='admin') {

       if($this->editAccess || K_Access::accessSiteCheck($accessRes)){
     	      if(K_Access::accessTree($nodeId,array('add','addremove'),true) || K_Access::accessTree($nodeId,array('edit'))){
    	       echo' data-edited-block = "'.$nodeId.'" ';
              }
              $this->editAccess = true;
    	   }
  	   }   
       
       
 	public function start($nodeId, $accessRes = 'admin') {
       if($this->editAccess || K_Access::accessSiteCheck($accessRes)){
 	      if(K_Access::accessTree($nodeId)){
	       
            echo '<div class="edit-wrapper">
            
            <div class="mod-panel">
            <a class="edit-button"  href="javascript:;" id="edit_'.$nodeId.'">
                     <img src="/usr/img/edit.png">
            </a>
            <a class="fast-edit-button"  href="javascript:;" id="edit_'.$nodeId.'">
                     <img src="/usr/img/edit2.png">
            </a>
            </div>
            ';
                
	      }
          $this->editAccess=true;
	   }
	}
    
    
    public function end( ) {
        if($this->editAccess)
        {
     	  echo '</div>';
        }
	}
}

?>