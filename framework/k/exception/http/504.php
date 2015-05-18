<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_504 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 504 Gateway Timeout
	 */
	protected $_code = 504;

}