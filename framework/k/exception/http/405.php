<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_405 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 405 Method Not Allowed
	 */
	protected $_code = 405;

}