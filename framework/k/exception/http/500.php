<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_500 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 500 Internal Server Error
	 */
	protected $_code = 500;

}