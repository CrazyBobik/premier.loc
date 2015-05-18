<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_BlockEdit extends Controller {

	var  $layout = 'edit_layout';
    public $helpers = array('call');
   
    public function indexAction() {
        $this->disableLayout=true;
        $this->view->key = intval($this->getParam('key'));
    }
 
}
