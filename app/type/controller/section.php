<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Section extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('gallery');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1');

	/* {private} */
	private $typeSectionTable;

	public function onInit()
	{
		$this->typeSectionTable = new Type_Model_Section();

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
			$result = $this->typeSectionTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_section_item');
		}
		else
		{
			$result = $this->typeSectionTable->select()->fetchArray();
			$this->render('type_section');
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

			$insertId = $this->typeSectionTable->save($valuesToAdd);
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

			$insertId = $this->typeSectionTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeSectionTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
