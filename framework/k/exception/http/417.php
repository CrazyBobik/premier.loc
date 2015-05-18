<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_417 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 417 Expectation Failed
	 */
	protected $_code = 417;

}