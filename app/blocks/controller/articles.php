<?php

class Blocks_Controller_Articles  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

		$this->view->item = K_Registry::read('articles');
	
		K_Crumbs::add($this->view->item['header'],'/'.$this->view->item['tree_name']);

		$this->render('articles'); 
 	}
  
}