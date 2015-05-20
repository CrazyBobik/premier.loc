<?php

class Blocks_Controller_NewsOnMain  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

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
 	}
  
}