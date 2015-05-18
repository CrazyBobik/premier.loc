<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_503 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 503 Service Unavailable
	 */
	protected $_code = 503;

}