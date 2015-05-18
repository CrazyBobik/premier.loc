<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Menucat extends Model {
	public $name = 'type_menucat';
	public $primary = 'type_menucat_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_menucat_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
