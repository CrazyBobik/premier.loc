<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Articles extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('articles');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1', '2', '3', '4');

	/* {private} */
	private $typeArticlesTable;

	public function onInit()
	{
		$this->typeArticlesTable = new Type_Model_Articles();

	}

	/* {actions} */
	public function indexAction()
	{
		$this->showAction();
	}

	public function showAction()
	{
		if ($this->getParam('link'))
		{
			$result = $this->typeArticlesTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_articles_item');
		}
		else
		{
			$result = $this->typeArticlesTable->select()->fetchArray();
			$this->render('type_articles');
		}
	}

	public function createAction()
	{
		$valuesToAdd = array();
		if (isset($_POST) && !empty($_POST))
		{
			foreach ($_POST as $key => $value)
			{
				if (in_array($key, $this->fields))
				{
					$valuesToAdd[$key] = $value;
				}
			}

			$insertId = $this->typeArticlesTable->save($valuesToAdd);
		}
	}

	public function updateAction()
	{
		$valuesToUpdate = array();
		if (isset($_POST) && !empty($_POST) && $this->getParam(0))
		{
			foreach ($_POST as $key => $value)
			{
				if (in_array($key, $this->fields))
				{
				$valuesToUpdate[$key] = $value;
				}
			}

			$insertId = $this->typeArticlesTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeArticlesTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
