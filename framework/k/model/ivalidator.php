<?php 

interface K_Model_IValidator {
	
	public function __construct( $validate = null );
	
	public function valid( &$data = null, $validate = null );	
}

?>