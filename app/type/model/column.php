<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Column extends Model {
	public $name = 'type_column';
	public $primary = 'type_column_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_column_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
