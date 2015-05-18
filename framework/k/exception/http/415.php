<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_415 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 415 Unsupported Media Type
	 */
	protected $_code = 415;

}