<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Country extends Model {
	public $name = 'type_country';
	public $primary = 'type_country_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_country_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
