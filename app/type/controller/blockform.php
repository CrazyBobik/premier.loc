<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Blockform extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array();
	public static $allowedParents = array();
	public static $fields = array('0');

	/* {private} */
	private $typeBlockformTable;

	public function onInit()
	{
		$this->typeBlockformTable = new Type_Model_Blockform();

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
			$result = $this->typeBlockformTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_blockform_item');
		}
		else
		{
			$result = $this->typeBlockformTable->select()->fetchArray();
			$this->render('type_blockform');
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

			$insertId = $this->typeBlockformTable->save($valuesToAdd);
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

			$insertId = $this->typeBlockformTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeBlockformTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
