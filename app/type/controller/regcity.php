<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Regcity extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Все');
	public static $allowedParents = array('region');
	public static $fields = array('0', '1', '2', '3');

	/* {private} */
	private $typeRegcityTable;

	public function onInit()
	{
		$this->typeRegcityTable = new Type_Model_Regcity();

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
			$result = $this->typeRegcityTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_regcity_item');
		}
		else
		{
			$result = $this->typeRegcityTable->select()->fetchArray();
			$this->render('type_regcity');
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

			$insertId = $this->typeRegcityTable->save($valuesToAdd);
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

			$insertId = $this->typeRegcityTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeRegcityTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
