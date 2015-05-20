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

        $this->view->newsId = K_TreeQuery::gOne('/settings/newsOnMain/', 'newsonmain');


        $idsArray[]=$this->view->newsId['znew1'];
        $idsArray[]=$this->view->newsId['znew2'];
        $idsArray[]=$this->view->newsId['znew3'];

        $this->view->newsz = K_TreeQuery::getNodes($idsArray, 'news');

        $idsArray = array();
        $idsArray[]=$this->view->newsId['knew1'];
        $idsArray[]=$this->view->newsId['knew2'];
        $idsArray[]=$this->view->newsId['knew3'];

        $this->view->newsk = K_TreeQuery::getNodes($idsArray, 'news');

        $this->render('newsOnMain');
		$this->render('newsOnMain'); 
 	}
  
}