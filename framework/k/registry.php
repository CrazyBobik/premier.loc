<?php 

/**
 * Class K_Registry
 * <example>
 * $db = K_Registry::get('db');
 * $db->useCache( K_Registry::get('cacheManager')->cache('sql') );
 * </example>
 */

class K_Registry {	
	protected static $data = array();

	protected function __construct() {}

	public static function read( $key ) {
		return isset(self::$data[ $key ])?self::$data[ $key ]:null;
	}
	
	public static function get( $key ) {
		return self::read( $key );
	}
	
	public static function write( $key, &$value ) {
		self::$data[ $key ] = $value;
	}
	
	public static function set( $key, &$value ) {
		self::write( $key, $value );
	}
	
	public static function test( $key ) {
		return isset(self::$data[ $key ]);
	}
	
	public static function remove( $key ) {
		if ( isset(self::$data[ $key ]) ) {
			unset(self::$data[ $key ]);
		}
	}
}

?>