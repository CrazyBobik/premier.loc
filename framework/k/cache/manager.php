<?php

/**
 * Cache Manager & Cache Factory
 */

define('K_CACHE_SIMPLE', 'Simple');
define('K_CACHE_MEMCACHED', 'Memcached');

define('K_TAGS_INCLUDE', 'K_TAGS_INCLUDE');
define('K_TAGS_NOTINCLUDE', 'K_TAGS_NOTINCLUDE');

class K_Cache_Manager {
	protected static $caches = array();
	
	protected $factory;
		
	protected $options = array (
		// Cache class name
		'class' => K_CACHE_SIMPLE,
	
		// Cache default life time
		'lifetime' => 90,
	
		// Cache files prefix
		'prefix' => 'K_',

		// Cache directory path
		'cache_dir' => '/cache',
		
		// directory access
		'chmod' => 0777,
	);
	
	/*
	 * Create new cache factory use options
	 */
	public function __construct( $cacheName, $options ) {
		self::$caches[ $cacheName ] = $this;
		$this->options = array_merge( $this->options, $options );
		
		if (!is_dir($this->options['cache_dir'])) {
			mkdir($this->options['cache_dir'], $this->options[ 'chmod' ]);
			chmod($this->options['cache_dir'], $this->options[ 'chmod' ]);
		}
		
		$this->options['cache_dir'] = realpath($this->options['cache_dir']);
				
		$cacheClassName = 'K_Cache_'.$this->options['class'];		
		$this->factory = new $cacheClassName( $this, $this->options );
	}
	
	/*
	 * Return current cache factory for working with cache
	 */
	public function factory() {
		return $this->factory;
	}
	
	/*
	 * Return cache factory by name 
	 */
	public static function get( $cacheName ) {
		return self::$caches[ $cacheName ]->factory;
	}
	
	public static function cache( $cacheName ) {
		return self::get( $cacheName );
	}
	
	/*
	 * Remove cache factory
	 */
	public static function _remove( $cacheName ) {
		unset( self::$caches[ $cacheName ] );
	}
}

?>