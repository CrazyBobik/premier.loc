<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Link extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('link','menucat','productlink');
	public static $allowedParents = array();
	public static $fields = array('0', '1', '2', '3');

	/* {private} */
	private $typeLinkTable;

	public function onInit()
	{
		$this->typeLinkTable = new Type_Model_Link();

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
			$result = $this->typeLinkTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_link_item');
		}
		else
		{
			$result = $this->typeLinkTable->select()->fetchArray();
			$this->render('type_link');
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

			$insertId = $this->typeLinkTable->save($valuesToAdd);
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

			$insertId = $this->typeLinkTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeLinkTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
