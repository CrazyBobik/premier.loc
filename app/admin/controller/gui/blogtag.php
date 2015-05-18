<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');


			class Admin_Controller_Gui_Blogtag extends Admin_Controller_Gui {

	
				
				public function __construct($nodeData)
	
				{
		
					parent::__construct($this->_options);

		
					
					$this->nodeData = $nodeData;
	
				}

			}