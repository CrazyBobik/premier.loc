<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Filter extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('folder','list');
	public static $allowedParents = array('page');
	public static $fields = array('0', '1', '2');

	/* {private} */
	private $typeFilterTable;

	public function onInit()
	{
		$this->typeFilterTable = new Type_Model_Filter();

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
			$result = $this->typeFilterTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_filter_item');
		}
		else
		{
			$result = $this->typeFilterTable->select()->fetchArray();
			$this->render('type_filter');
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

			$insertId = $this->typeFilterTable->save($valuesToAdd);
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

			$insertId = $this->typeFilterTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeFilterTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
