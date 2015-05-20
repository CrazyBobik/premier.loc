<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Reklama extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('Нет');
	public static $allowedParents = array('Все');
	public static $fields = array('0', '1');

	/* {private} */
	private $typeReklamaTable;

	public function onInit()
	{
		$this->typeReklamaTable = new Type_Model_Reklama();

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
			$result = $this->typeReklamaTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_reklama_item');
		}
		else
		{
			$result = $this->typeReklamaTable->select()->fetchArray();
			$this->render('type_reklama');
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

			$insertId = $this->typeReklamaTable->save($valuesToAdd);
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

			$insertId = $this->typeReklamaTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeReklamaTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
