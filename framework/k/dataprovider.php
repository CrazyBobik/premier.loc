<?php 

class K_DataProvider {
	static private $_instance = null;
	
	public function __construct() {
		self::$_instance = $this;
	}
	
	public static function get() {
		if ( !self::_instance ) {
			new K_DataProvider();			
		}
		return self::_instance;
	}
}

?>