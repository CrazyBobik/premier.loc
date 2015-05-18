<?php 

interface K_Cache_ICache {
	public function __construct( $dmCacheObject, &$options );
	
	public function save( $cacheID, &$data, $param1 = 0, $param2 = 0 );
	
	public function load( $cacheID );
	
	public function clear( $cacheID );
	
	public function test( $cacheID );
	
	public function remove( $cacheID );
}

?>