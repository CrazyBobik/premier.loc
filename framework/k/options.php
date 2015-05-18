<?php 

/**
 * Class Options
 */

class K_Options {	
	/**
	 * Save serialized array
	 */
	public static function save( $data, $path ) {
		file_put_contents( $path, serialize($data) );
	}
	
	/**
	 * Load serialized array
	 */	
	public static function load( $path ) {
		if (file_exists($path)) {
			return unserialize( file_get_contents( $path ) );
		} else {
			throw new Exception('Options file('.$path.') not found.');
		}
	}
}

?>