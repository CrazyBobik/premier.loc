<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_411 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 411 Length Required
	 */
	protected $_code = 411;

}