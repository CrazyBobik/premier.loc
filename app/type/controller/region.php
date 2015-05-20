<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Region extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('city');
	public static $allowedParents = array('country');
	public static $fields = array('0', '1', '2', '3');

	/* {private} */
	private $typeRegionTable;

	public function onInit()
	{
		$this->typeRegionTable = new Type_Model_Region();

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
			$result = $this->typeRegionTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_region_item');
		}
		else
		{
			$result = $this->typeRegionTable->select()->fetchArray();
			$this->render('type_region');
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

			$insertId = $this->typeRegionTable->save($valuesToAdd);
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

			$insertId = $this->typeRegionTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeRegionTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
