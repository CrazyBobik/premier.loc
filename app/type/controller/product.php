<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Product extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Нет');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1', '2', '3', '4');

	/* {private} */
	private $typeProductTable;

	public function onInit()
	{
		$this->typeProductTable = new Type_Model_Product();

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
			$result = $this->typeProductTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_product_item');
		}
		else
		{
			$result = $this->typeProductTable->select()->fetchArray();
			$this->render('type_product');
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

			$insertId = $this->typeProductTable->save($valuesToAdd);
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

			$insertId = $this->typeProductTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeProductTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
