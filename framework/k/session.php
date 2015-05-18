<?php 

/**
 * Class K_Session
 * <example>
 * $db = K_Registry::get('db');
 * $db->useCache( K_Registry::get('cacheManager')->cache('sql') );
 * </example>
 */

class K_Session {	
	protected static $data = array();

	protected function __construct() {}

	public static function read( $key, $parent=null ) {
		if ($parent) {
			return self::test( $key, $parent )?$_SESSION[ $parent ][ $key ]:null;	
		}
		return isset($_SESSION[ $key ])?$_SESSION[ $key ]:null;
	}
	
	public static function get( $key, $parent=null ) {
		return self::read( $key, $parent );
	}
	
	public static function write( $key, $value, $parent=null ) {
		if ( $parent ) {
			if ( !isset($_SESSION[ $parent ]) ) {
				$_SESSION[ $parent ] = array();
			}
			$_SESSION[ $parent ][ $key ] = $value;
			return;
		} 
		$_SESSION[ $key ] = $value;
	}
	
	public static function set( $key, $value, $parent=null ) {
		self::write( $key, $value, $parent );
	}
	
	public static function test( $key, $parent=null ) {
		if ($parent) {
			return isset($_SESSION[ $parent ])&&isset($_SESSION[ $parent ][ $key ]);
		}
		return isset($_SESSION[ $key ]);
	}
	
	public static function remove( $key, $parent=null ) {
		if ( $parent ) {
			if ( self::test( $key, $parent ) ) {
				unset($_SESSION[ $parent ][ $key ]);
			}
			return;
		}
		if ( self::test( $key ) ) {
			unset($_SESSION[ $key ]);
		}
	}
}

?>