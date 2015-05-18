<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Blogtag extends Model {
	public $name = 'type_blogtag';
	public $primary = 'type_blogtag_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_blogtag_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
