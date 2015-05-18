<?php

class Blocks_Controller_SliderNovosroy  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->slider1 = K_TreeQuery::crt("/sliderNovostroy/slider1/")->type()->go(array('aliases' => true, 'childs' => true));

		$this->render('sliderNovosroy'); 
 	}
  
}