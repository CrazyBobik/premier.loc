<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Produkciya extends Model {
	public $name = 'type_produkciya';
	public $primary = 'type_produkciya_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_produkciya_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
