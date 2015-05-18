<?php

class Blocks_Controller_Newonmain  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {	
	
		
		if($_SERVER['REMOTE_ADDR']=='95.135.123.100'){
		
			$this->view->items = K_TreeQuery::crt('/newonmain/'.Allconfig::$contentLang.'/')->type(array('newonmain'))->limit(2)->go();
		
		
		}else{
		
	
		
		}
				$this->view->items = K_TreeQuery::crt('/newonmain/'.Allconfig::$contentLang.'/')->type(array('newonmain'))->condit(array('newonmain'=>" and type_newonmain_shownew !='Нет' "))->limit(2)->go();
	
		$this->render('newonmain'); 
 	}
  
}