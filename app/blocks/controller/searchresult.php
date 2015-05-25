<?php

class Blocks_Controller_Searchresult  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $where = array();

        if (isset($_POST['country']) && !empty($_POST['country'])) {
            $where['country'] = $_POST['country'];
        }
        if (isset($_POST['region']) && !empty($_POST['region'])) {
            $where['region'] = $_POST['region'];
        }
        if (isset($_POST['city']) && !empty($_POST['city'])){
            $where['city'] = $_POST['city'];
        }
        if (isset($_POST['type'])  && !empty($_POST['type'])){
            $where['type'] = $_POST['type'];
        }
        if (isset($_POST['market']) && !empty($_POST['market'])){
            $where['market'] = $_POST['market'];
        }
        if (isset($_POST['sq']) && !empty($_POST['sq'])){
            $where['sq'] = $_POST['sq'];
        }
        if (isset($_POST['rooms']) && !empty($_POST['rooms'])){
            $where['rooms'] = $_POST['rooms'];
        }
        if (isset($_POST['state']) && !empty($_POST['state'])){
            $where['state'] = $_POST['state'];
        }
        if (isset($_POST['id']) && !empty($_POST['id'])){
            $where['id'] = $_POST['id'];
        }
        if (isset($_POST['price_from']) && !empty($_POST['price_from'])){
            $where['price_from'] = $_POST['price_from'];
        }
        if (isset($_POST['price_to']) && !empty($_POST['price_to'])){
            $where['price_to'] = $_POST['price_to'];
        }

        $this->view->result = spot::seacrhe($where);

		$this->render('searchresult'); 
 	}
  
}