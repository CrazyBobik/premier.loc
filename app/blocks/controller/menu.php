<?php

class Blocks_Controller_Menu  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->menu = K_TreeQuery::crt("/menu/")->type(array("menu"))->go();
		$this->render('menu'); 
 	}
  
}