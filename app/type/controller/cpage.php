<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Cpage extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('block','cpage','page','column');
	public static $allowedParents = array('Нет');
	public static $fields = array('0');

	/* {private} */
	private $typeCpageTable;

	public function onInit()
	{
		$this->typeCpageTable = new Type_Model_Cpage();

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
			$result = $this->typeCpageTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_cpage_item');
		}
		else
		{
			$result = $this->typeCpageTable->select()->fetchArray();
			$this->render('type_cpage');
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

			$insertId = $this->typeCpageTable->save($valuesToAdd);
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

			$insertId = $this->typeCpageTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeCpageTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
