<?php 
class Dev_Controller_Index extends Controller {

	public $helpers = array('paginator', 'call', 'error', 'form', 'include', 'ru');
	public $layout = 'layout';

	private $test = 'test';
   
	public function indexAction() {
		$this->view->title = 'Средства разработчика.';
		$this->view->header = 'Добро пожаловать';
		$this->view->bigtable = false;
		
        $this->render('index');
	}
	

	private function getTestMessageFromDb()
	{
		$result = array();
		
		$testTable = new Default_Model_Test();

		$testList = $testTable->fetchRow(
			K_Db_Select::create()
				->limit(1)
		);

		$result = $testList->toArray();
		
		return $result;
	}

}