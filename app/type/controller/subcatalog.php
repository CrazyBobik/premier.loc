<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Subcatalog extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('colors','textures','doors','cloths','decors');
	public static $allowedParents = array('Все');
	public static $fields = array('0');

	/* {private} */
	private $typeSubcatalogTable;

	public function onInit()
	{
		$this->typeSubcatalogTable = new Type_Model_Subcatalog();

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
			$result = $this->typeSubcatalogTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_subcatalog_item');
		}
		else
		{
			$result = $this->typeSubcatalogTable->select()->fetchArray();
			$this->render('type_subcatalog');
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

			$insertId = $this->typeSubcatalogTable->save($valuesToAdd);
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

			$insertId = $this->typeSubcatalogTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeSubcatalogTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
