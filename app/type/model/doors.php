<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Doors extends Model {
	public $name = 'type_doors';
	public $primary = 'type_doors_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_doors_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
