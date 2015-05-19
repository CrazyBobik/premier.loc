<?php

class Blocks_Controller_Slider extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->slider = K_TreeQuery::crt("/slider/")->type(array('slideritem'))->go();

		$this->render('slider'); 
 	}
  
}