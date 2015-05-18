<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_414 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 414 Request-URI Too Long
	 */
	protected $_code = 414;

}