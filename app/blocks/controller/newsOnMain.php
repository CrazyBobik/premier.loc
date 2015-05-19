<?php

class Blocks_Controller_NewsOnMain  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

//        $page = 1;
//
//        // how many records per page
//        $size = 10;
//
//        // we get the current page from $_GET
//        if (isset($_GET['page'])){
//            $this->view->page = (int) $_GET['page'];
//        }
//
//        $pag_info = K_Paginator::prepear($this->view->page, $size);
//
//        list($this->view->news, $this->view->countItems)  = K_TreeQuery::crt("/news/ru/")->type(array('news'))->limitLikeSql($pag_info['start'], $pag_info['onPage'])->go(array('childs'=>true,'count'=>true,'orderby'=>'DESC'));
//        $this->view->pages = ceil($this->view->countItems/$pag_info['onPage']);

        $this->view->newsOut = K_TreeQuery::crt("/news/news-out/")->type(array("news"))->go();
        $this->view->newsIn = K_TreeQuery::crt("/news/news-compain/")->type(array("news"))->go();

		$this->render('newsOnMain'); 
 	}
  
}