<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_401 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 401 Unauthorized
	 */
	protected $_code = 401;

}