<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_403 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 403 Forbidden
	 */
	protected $_code = 403;

}