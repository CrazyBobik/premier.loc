<?php

/**
 * Database expression
 * Insert original string into sql 
 */

class K_Db_Expr {
	protected $value = '';
	
	public function __construct( $string ) {
		$this->value = $string;
	}
	
	public function __toString() {
		return $this->value;
	}
};


function expr($text) {
	return new K_Db_Expr( $text );
}
?>