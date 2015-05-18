<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_Gui_Gallery extends Admin_Controller_Gui {
	
                public function __construct($nodeData)
	     		{
	
					parent::__construct($this->_options);
			$this->nodeData = $nodeData;

				}
                    
                    
            	protected function seoGUI()
            	{
            	   
                     if(! K_Access::acl()->isAllowed(K_Auth::getRoles(), 'admin/tree/updateseo', true)){
            	       
            	    	return false;
                        
              	    };
                   
            		$this->tabs['seo'] = 'SEO';
            	
            		$this->view->node = $this->nodeData;
            	           	           		
            		return $this->x_render('seo', $this);
            	}
}