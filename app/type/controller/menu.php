<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Menu extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Нет');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1', '2');

	/* {private} */
	private $typeMenuTable;

	public function onInit()
	{
		$this->typeMenuTable = new Type_Model_Menu();

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
			$result = $this->typeMenuTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_menu_item');
		}
		else
		{
			$result = $this->typeMenuTable->select()->fetchArray();
			$this->render('type_menu');
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

			$insertId = $this->typeMenuTable->save($valuesToAdd);
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

			$insertId = $this->typeMenuTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeMenuTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
