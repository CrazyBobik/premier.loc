<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_501 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 501 Not Implemented
	 */
	protected $_code = 501;

}