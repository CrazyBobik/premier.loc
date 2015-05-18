<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Menucat extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('productlink');
	public static $allowedParents = array('menulink');
	public static $fields = array('0');

	/* {private} */
	private $typeMenucatTable;

	public function onInit()
	{
		$this->typeMenucatTable = new Type_Model_Menucat();

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
			$result = $this->typeMenucatTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_menucat_item');
		}
		else
		{
			$result = $this->typeMenucatTable->select()->fetchArray();
			$this->render('type_menucat');
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

			$insertId = $this->typeMenucatTable->save($valuesToAdd);
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

			$insertId = $this->typeMenucatTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeMenucatTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
