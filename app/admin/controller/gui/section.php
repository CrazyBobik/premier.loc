<?php 

class Admin_Controller_Gui_Section extends Admin_Controller_Gui {

	
				
                    public function __construct($nodeData)
	
    				{
		
    					parent::__construct($this->_options);

		
    					
    					$this->nodeData = $nodeData;
	
    				}
                    
                    
            	protected function seoGUI()
            	{
            		$this->tabs['seo'] = 'SEO';
            	
            		$this->view->node = $this->nodeData;
            	           	           		
            		return $this->x_render('seo', $this);
            	}
}