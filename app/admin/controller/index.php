<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_Index extends Controller {
	
	/* {public} */
	public $helpers = array('paginator', 'call', 'error', 'form', 'include', 'ru');
	public $layout = 'layout';
	
	/* {private} */
	private $test = 'test';
    
	/* {actions} */
	public function indexAction() {
		$this->view->title = 'Панель администратора.';
		$this->view->header = 'Добро пожаловать';
		$this->view->ajaxed = true;
		
		//K_Tree::add(7, 'form', 'edit_element', 'Редактирование элемента (нода)');
		//K_Tree::add(7, 'form', 'update_type', 'Редактирование типа');
		
		//K_Tree::move(2, 3, 'before');
		
		/*$fields = array(
			'title' => array(
				'type'    => 'string',
				'title'   => 'Заголовок',
				'pattern' => false,
			),
			'content' => array(
				'type'    => 'text',
				'title'   => 'Содержимое',
				'pattern' => false,
			),*/
			/* Базовые поля, необходимые для генерации нода в дереве автоматически вставляются в начало, если их не заменить в массиве (но не желательно так делать) */
		/*);
		
		$allowedChildren = array(
		);
		
		$allowedParents = array(
			'page', 
			'folder',
		);
		
		K_Tree_Types::add('form', 'Тип форма', $fields, $allowedChildren, $allowedParents);*/
		//K_Tree_Types::delete('hotel');
		//K_Tree_Types::update('page', 'Тип текстовая страница', $fields);
		
        $this->render('index');
	}
	
	/* {methods} */
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
	private function getNews()
	{
		$result = array();
		
		$newsTable = new Default_Model_News();

		$newsList = $newsTable->find(
			K_Db_Select::create()
				->_join('authors', '`news`.`news_author_id` = `authors`.`author_id`', 'inner')
				->_join('categories', '`news`.`news_category_id` = `categories`.`category_id`', 'inner')
				->order('news_id ASC')
		);

		if (count($newsList))
		{
			foreach ($newsList as $item)
			{
				$resultLink = $item->toArray();
				
				$resultLink['news_date'] = date('Y.m.d H:i:s', $resultLink['news_added_time']);
				
				$result[] = $resultLink;
			}
		}
		
		return $result;
	}
}