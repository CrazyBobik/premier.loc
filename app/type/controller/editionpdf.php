<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Editionpdf extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Нет');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1', '2', '3');

	/* {private} */
	private $typeEditionpdfTable;

	public function onInit()
	{
		$this->typeEditionpdfTable = new Type_Model_Editionpdf();

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
			$result = $this->typeEditionpdfTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_editionpdf_item');
		}
		else
		{
			$result = $this->typeEditionpdfTable->select()->fetchArray();
			$this->render('type_editionpdf');
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

			$insertId = $this->typeEditionpdfTable->save($valuesToAdd);
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

			$insertId = $this->typeEditionpdfTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeEditionpdfTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
