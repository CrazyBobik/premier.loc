<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Lang extends Model {
	public $name = 'type_lang';
	public $primary = 'type_lang_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_lang_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
