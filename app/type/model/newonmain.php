<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Newonmain extends Model {
	public $name = 'type_newonmain';
	public $primary = 'type_newonmain_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_newonmain_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
