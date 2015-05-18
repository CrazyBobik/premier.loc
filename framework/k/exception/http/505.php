<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_505 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 505 HTTP Version Not Supported
	 */
	protected $_code = 505;

}