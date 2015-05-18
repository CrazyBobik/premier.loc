<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Articles extends Model {
	public $name = 'type_articles';
	public $primary = 'type_articles_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_articles_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
