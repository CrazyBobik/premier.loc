<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_409 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 409 Conflict
	 */
	protected $_code = 409;

}