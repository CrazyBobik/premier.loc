<?php

class Blocks_Controller_Objects  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {
        $page = 1;

        // how many records per page
        $size = 10;

        // we get the current page from $_GET
        if (isset($_GET['page'])){
            $this->view->page = (int) $_GET['page'];
        }

        $pag_info = K_Paginator::prepear($this->view->page, $size);
        list($this->view->obj, $this->view->countItems)  = K_TreeQuery::crt("/jk/ru/")->type(array('novostoy'))->limitLikeSql($pag_info['start'], $pag_info['onPage'])->go(array('childs'=>true,'count'=>true,'orderby'=>'DESC'));
        $this->view->pages = ceil($this->view->countItems/$pag_info['onPage']);

		$this->render('objects'); 
 	}
  
}