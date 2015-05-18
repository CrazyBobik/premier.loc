<?php 

class K_Cache_Memcached implements K_Cache_ICache {
	
	protected $options = array(
		'host' => '127.0.0.1',
		'post' => 11211
	);

	protected $memcached;
	
	public function __construct( $dmCacheObject, &$options ) {
		if ( !empty($options) && is_array($options) ) {
			$this->options = array_merge( $this->options, $options );
		}
		$this->memcached = new Memcache();
	}

	public function addServer( $host = null, $port = 0 ) {
		$realHost = $this->options['host'];
		if ( !empty($host) ) {
			$realHost = $host;
		}

		$realPort = $this->options['port'];
		if ( !empty($port) ) {
			$realPort = $port;
		}

		$this->memcached->addServer( $host, $port );
	}

	public function inc( $cacheID, $value = 1 ) {
		$this->memcached->increment( $cacheID, $value );
	}
	
	public function increment( $cacheID, $value = 1 ) {
		$this->inc( $cacheID, $value );
	}

	public function dec( $cacheID, $value = 1 ) {
		$this->memcached->decrement( $cacheID, $value );
	}

	public function decrement( $cacheID, $value = 1 ) {
		$this->dec( $cacheID, $value );
	}

	public function connect( $host = null, $port = 0 ) {
		$realHost = $this->options['host'];
		if ( !empty($host) ) {
			$realHost = $host;
		}

		$realPort = $this->options['port'];
		if ( !empty($port) ) {
			$realPort = $port;
		}

		$this->memcached->connect( $realHost, $realPort );
	}

	public function close() {
		$this->memcached->close();
	}
	
	public function save( $cacheID, &$data, $expires = 0, $reserved = 0 ) { // 60*60*24 = 86400
		$this->memcached->set( $cacheID, $data, MEMCACHE_COMPRESSED, $expires );
	}
	
	public function load( $cacheID ) {					
		return $this->memcached->get( $cacheID, MEMCACHE_COMPRESSED );
	}
	
	public function clear( $cacheID ) {
		$this->memcached->delete( $cacheID );
	}

	public function replace( $cacheID, &$data, $expires = 0 ) {
		$this->memcached->replace( $cacheID, $data, MEMCACHE_COMPRESSED, $expires );
	}

	public function add( $cacheID, &$data, $expires = 0 ) {
		$this->memcached->add( $cacheID, $data, MEMCACHE_COMPRESSED, $expires );
	}
	
	public function test( $cacheID ) {
		clearstatcache();
		$path = $this->dmCacheSimpleIDs . $this->_sequensePathCreate( $cacheID, $this->options['cache_dir'] . K_CACHE_SIMPLE_IDS, false );
		$cacheMeta = $path.K_CACHE_SIMPLE_CACHE_META;
		if ( is_file($cacheMeta) ) {
			$meta = unserialize( file_get_contents( $cacheMeta ) );
			if ( $meta['expire'] < time() ) {
				$this->clear( $cacheID );
				return false;
			}
			return true;
		}
		return false;
	}
	
	public function remove( $cacheID ) {
		$this->clear( $cacheID );
	}

	public function flush() {
		$this->memcached->flush();
	}

	public function setCompressThreshold ( $threshold , $min_savings = 0.2 ) { // up to 0.5
		$this->memcached->setCompressThreshold ( $threshold , $min_savings );
	}
}

?>