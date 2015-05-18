<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Type_Controller_Site extends Controller {
	/* {public} */
	public static $allowedChildren = array('list');
	public static $allowedParents = array();
	public static $fields = array('title', 'content');
}
