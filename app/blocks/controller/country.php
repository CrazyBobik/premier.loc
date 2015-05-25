<?php

class Blocks_Controller_Country  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->country = K_Registry::read('country');

        $query = array();
        switch (count(K_Url::get()->expPath)){
            case 1:
                $query['country'] = $this->view->country['tree_id'];
                break;
            case 2:
               $query['region'] = $this->view->country['tree_id'];
                break;
            case 3:
               $query['city'] = $this->view->country['tree_id'];
                break;
        }

        $this->view->objects = spot::seacrhe($query);

		$this->render('country'); 
 	}
  
}