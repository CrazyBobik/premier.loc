<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Decors extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Нет');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1', '2', '3');

	/* {private} */
	private $typeDecorsTable;

	public function onInit()
	{
		$this->typeDecorsTable = new Type_Model_Decors();

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
			$result = $this->typeDecorsTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_decors_item');
		}
		else
		{
			$result = $this->typeDecorsTable->select()->fetchArray();
			$this->render('type_decors');
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

			$insertId = $this->typeDecorsTable->save($valuesToAdd);
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

			$insertId = $this->typeDecorsTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeDecorsTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
