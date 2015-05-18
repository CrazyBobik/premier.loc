<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Filial extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array();
	public static $allowedParents = array();
	public static $fields = array('0', '1', '2', '3', '4', '5', '6', '7');

	/* {private} */
	private $typeFilialTable;

	public function onInit()
	{
		$this->typeFilialTable = new Type_Model_Filial();

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
			$result = $this->typeFilialTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_filial_item');
		}
		else
		{
			$result = $this->typeFilialTable->select()->fetchArray();
			$this->render('type_filial');
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

			$insertId = $this->typeFilialTable->save($valuesToAdd);
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

			$insertId = $this->typeFilialTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeFilialTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
