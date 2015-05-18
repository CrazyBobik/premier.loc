<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Page extends Model {
	public $name = 'type_page';
	public $primary = 'type_page_id';
	public $foreign = array(
		'tree' => array(
			'type_page_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
