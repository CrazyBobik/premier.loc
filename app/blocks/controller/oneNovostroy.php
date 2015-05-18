<?php

class Blocks_Controller_OneNovostroy  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->novostroy = K_Registry::read('novostroy');
		$this->render('oneNovostroy'); 
 	}
  
}