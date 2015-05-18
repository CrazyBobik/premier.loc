<?php

/**
 * Data model
 */

class K_Model {
	var $data;
	var $name;
	var $primary;
	var $dataSource;
	
	var $options = array(
		
	);
	
	public function __construct( $name, $options ) {
		$this->name = $name;
		$this->options = array_merge( $this->options, $options );
	}
	
	
}

?>