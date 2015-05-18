<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Model_Image extends Model {
	public $name = 'type_image';
	public $primary = 'type_image_id';
	public $foreign = array(
		'K_Tree_Model' => array(
			'type_image_id' => array(
				'key' => 'tree_id',
				'type' => K_LINKTYPE_ONE_ONE,
				'delete' => 'cascade',
				'update' => 'none',
			)
		)
	);
}
