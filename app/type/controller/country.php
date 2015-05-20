<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Country extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Все');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1', '2', '3');

	/* {private} */
	private $typeCountryTable;

	public function onInit()
	{
		$this->typeCountryTable = new Type_Model_Country();

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
			$result = $this->typeCountryTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_country_item');
		}
		else
		{
			$result = $this->typeCountryTable->select()->fetchArray();
			$this->render('type_country');
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

			$insertId = $this->typeCountryTable->save($valuesToAdd);
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

			$insertId = $this->typeCountryTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeCountryTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
