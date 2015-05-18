<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_Exception_HTTP_416 extends K_exception_HTTP {

	/**
	 * @var   integer    HTTP 416 Request Range Not Satisfiable
	 */
	protected $_code = 416;

}