<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Lang extends Controller {

	/* {public} */
	public $layout = 'layout';
	public static $allowedChildren = array('articles','news','novostoy','country');
	public static $allowedParents = array('Все');
	public static $fields = array('0');

	/* {private} */
	private $typeLangTable;

	public function onInit()
	{
		$this->typeLangTable = new Type_Model_Lang();

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
			$result = $this->typeLangTable->select()->where('`tree_link` = '.$this->getParam('link'))->fetchRow()->toArray();
			$this->render('type_lang_item');
		}
		else
		{
			$result = $this->typeLangTable->select()->fetchArray();
			$this->render('type_lang');
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

			$insertId = $this->typeLangTable->save($valuesToAdd);
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

			$insertId = $this->typeLangTable->update($valuesToUpdate, '`tree_id`= '.$this->getParam(0).'');
		}
	}

	public function deleteAction()
	{
		if ($this->getParam(0))
		{
			$this->typeLangTable->select()->where('`tree_id` = '.$this->getParam(0))->remove();
		}
	}

}
