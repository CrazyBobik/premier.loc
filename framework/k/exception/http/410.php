<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_410 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 410 Gone
	 */
	protected $_code = 410;

}