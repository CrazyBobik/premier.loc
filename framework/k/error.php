<?php 

/**
 * Error handler & logger
 */

set_error_handler ("K_error_handler");

function K_error_handler($code, $msg, $file, $line) {
   // echo 'code: '.$code.'message: '.$msg.' file: '.$file.' line:  '.$line;
	K_debug::get()->addError('code: '.$code.'message: '.$msg.' file: '.$file.' line:  '.$line);
}

class K_Error {
	var $class;
	var $method;
	var $debugData;
	
	public function __construct( $string ) {
		$this->class = get_called_class();
		$this->method = __METHOD__;
		$this->debugData = debug_backtrace();
		throw new Exception( $string );
	}
}

?>