<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Submenu extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('link','alias');
	public static $allowedParents = array('Все');
	public static $fields = array('0');

	/* {private} */
	private $typeSubmenuTable;

	public function onInit()
	{
		$this->typeSubmenuTable = new Type_Model_Submenu();

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
			$result = $this->typeSubmenuTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_submenu_item');
		}
		else
		{
			$result = $this->typeSubmenuTable->select()->fetchArray();
			$this->render('type_submenu');
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

			$insertId = $this->typeSubmenuTable->save($valuesToAdd);
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

			$insertId = $this->typeSubmenuTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeSubmenuTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
