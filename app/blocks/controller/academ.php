<?php

class Blocks_Controller_Academ  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {
	
		$this->render('academ'); 
 	}
  
}