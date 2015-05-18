<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Tree_Forms {
	
	protected $formName;
	
	private $treeTable;
	private $formData;
	private $nodeData;
	
	public function __construct($formName)
	{
		$this->treeTable = new K_Tree_Model();
		$this->formName = $formName;
		$this->loadData();
	}
	
	public function show()
	{
		$formSources = unserialize($this->formData['type_'.$this->nodeData['tree_type'].'_content']);
		$formSources['form_structure'] = json_decode($formSources['form_structure']);
		
		return $formSources['form_structure'];
	}
	
	private function loadData()
	{
		$this->nodeData = $this->treeTable->select()->where('`tree_name`="'.$this->formName.'" AND `tree_type`="form"')->fetchRow()->toArray();
		
		if (!$this->nodeData)
		{
			throw new Exception("Form not found!");
		}
		
		$typeModelName = 'Type_Model_'.ucfirst($this->nodeData['tree_type']);
		$typeTable = new $typeModelName();
		
		$this->formData = $typeTable->select()->where('`type_'.$this->nodeData['tree_type'].'_id` = '.(int)$this->nodeData['tree_id'])->fetchRow()->toArray();
	}
	
}