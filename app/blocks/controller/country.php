<?php

class Blocks_Controller_Country  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->country = K_Registry::read('country');
		$this->render('country'); 
 	}
  
}