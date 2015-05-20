<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Regcity extends Model {
	public $name = 'type_regcity';
	public $primary = 'type_regcity_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_regcity_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
