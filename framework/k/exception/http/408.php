<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_408 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 408 Request Timeout
	 */
	protected $_code = 408;

}