<?php

class Blocks_Controller_Menu  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $this->view->menu = K_TreeQuery::crt("/menu/")->type(array("menu"))->go();
        $this->view->regionForCountry = array();

        $region = K_TreeQuery::crt("/allcountry/")->type(array('region'))->go();
        $country = K_TreeQuery::crt("/allcountry/")->type(array('country'))->go();
        foreach ($country as $c){
//            var_dump($c['tree_id']);
            $arr = array();
            foreach ($region as $r){
                if ($r['tree_pid'] == $c['tree_id']){
                    $arr[] = $r;
                }
//                var_dump($arr);
            }
            $this->view->regionForCountry[$c['tree_name']] = $arr;
        }

		$this->render('menu'); 
 	}
  
}