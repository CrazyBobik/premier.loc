<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_407 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 407 Proxy Authentication Required
	 */
	protected $_code = 407;

}