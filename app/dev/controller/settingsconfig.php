<?php

class Dev_Controller_Settingsconfig  extends K_Controller_Dev {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {
	
		$this->render('settingsconfig'); 
 	}
  
}