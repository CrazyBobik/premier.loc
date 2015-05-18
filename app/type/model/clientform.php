<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Clientform extends Model {
	public $name = 'type_clientform';
	public $primary = 'type_clientform_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_clientform_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
