<?php

class Blocks_Controller_Map  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->obj = K_TreeQuery::crt("/jk/")->type(array("novostoy"))->go();
	
		$this->render('map'); 
 	}
  
}