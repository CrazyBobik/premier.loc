<?php

class Blocks_Controller_SliderDoc  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->img = K_TreeQuery::crt("/sliderDoc/")->type(array("image"))->go();
	
		$this->render('sliderdoc');
 	}
  
}