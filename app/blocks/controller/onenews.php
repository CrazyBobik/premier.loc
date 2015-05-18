<?php

class Blocks_Controller_Onenews  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {
        $this->view->news = K_Registry::read('news');
		$this->render('onenews'); 
 	}
  
}