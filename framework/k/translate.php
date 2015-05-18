<?php 

define('TRANSLATE_EXTENSION', '.txt');

class K_Translate {	
	protected static $rootDirectory = '';

	protected function __construct() {}
	
	public static function setDirectory( $path ) {
		if ( is_dir($path) ) {
			self::$rootDirectory = $path;
			bindtextdomain("messages", $path);
			textdomain("messages");
			bind_textdomain_codeset ( "messages" , "utf-8" );
		}
	}	
}

?>