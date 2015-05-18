<?php

class Blocks_Controller_Altair  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {
	
		$this->render('altair'); 
 	}
  
}