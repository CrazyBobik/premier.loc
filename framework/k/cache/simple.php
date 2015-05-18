<?php 

define('K_CACHE_SIMPLE_TAGS', '/tags');
define('K_CACHE_SIMPLE_IDS', '/ids');
define('K_CACHE_SIMPLE_CACHE_OBJECT', '.data');
define('K_CACHE_SIMPLE_CACHE_META', '.meta');
define('K_CACHE_SIMPLE_TAG_OBJECT', '.tags');

class K_Cache_Simple implements K_Cache_ICache {
	
	protected $options = array (
		// Cache default life time
		'lifetime' => 90,

		// Cache files prefix
		'prefix' => 'K_',

		// Max directory level
		'dir_level' => 2,

		// Top directories name size
		'dir_name_size' => 32,

		// lock files on write
		'lock' => true,
	);
	
	protected $dmCacheSimpleIDs;
	protected $dmCacheSimpleTags;
	
	public function __construct( $dmCacheObject, &$options ) {
		if ( !empty($options) && is_array($options) ) {
			$this->options = array_merge( $this->options, $options );	
		}
		
		$this->dmCacheSimpleIDs = $this->options['cache_dir'] . K_CACHE_SIMPLE_IDS;
		$this->dmCacheSimpleTags = $this->options['cache_dir'] . K_CACHE_SIMPLE_TAGS;
		if ( !is_dir($this->dmCacheSimpleIDs) ) {
			mkdir( $this->dmCacheSimpleIDs, $this->options[ 'chmod' ] );
			chmod( $this->dmCacheSimpleIDs, $this->options[ 'chmod' ] );
		}
	}
	
	protected function _sequensePathCreate( $string, $dir, $create = true ) {
		if (strlen($string)) {
			$path = '/';
			
			$reqSize = $this->options[ 'dir_name_size' ] * $this->options[ 'dir_level' ];
			if (strlen($string) < $reqSize ) {
				$string = str_pad($string, $reqSize, "0", STR_PAD_RIGHT);
			}
			
			for ($i = 0; $i < $this->options[ 'dir_level' ]-1; $i++) {
				$path .= substr( $string, $i*$this->options[ 'dir_name_size' ], $this->options[ 'dir_name_size' ] );
				$dirPath = $dir.$path;
				if ( !is_dir($dirPath) && $create ) {
					mkdir($dirPath, $this->options[ 'chmod' ] );
					chmod($dirPath, $this->options[ 'chmod' ] );
				}
				$path .= '/';			
			}
			
			$fNameSize = ($this->options[ 'dir_level' ]-1)*$this->options[ 'dir_name_size' ];
			$path .= substr( $string, $fNameSize, strlen($string)-$fNameSize );		
			$dirPath = $dir.$path;	
					
			return $path;
		}
		throw new Exception('Can`t create cache folder. '.$string.', '.$dir);
		return $dir;
	}
	
	public function save( $cacheID, &$data, $tags = array(), $lifetime = 0 ) {
		if (is_dir($this->options['cache_dir'])) {
			
			if ( $lifetime == 0 ) {
				$lifetime = $this->options['lifetime'];
			}
			
			$path = $this->dmCacheSimpleIDs . $this->_sequensePathCreate( $cacheID, $this->dmCacheSimpleIDs );	
			$cacheFile = $path.K_CACHE_SIMPLE_CACHE_OBJECT;
			
			file_put_contents( $cacheFile, serialize( $data ), $this->options['lock']?LOCK_EX:0 );
			
			$expire = time()+$lifetime;
			$tagsData = array();
						
			if ( count($tags) ) {
				$tagsRoot = $this->dmCacheSimpleTags;	
				foreach($tags as $tagName) {
					
					if ( !is_dir($tagsRoot) ) {
						mkdir( $tagsRoot, $this->options[ 'chmod' ] );
						chmod( $tagsRoot, $this->options[ 'chmod' ] );
					}
					
					$tagPath = $this->_sequensePathCreate( $tagName, $tagsRoot );
					$tagFile = $tagsRoot.$tagPath.K_CACHE_SIMPLE_TAG_OBJECT;

					$tagInfo = array(
						'tag' => $tagName,
						'expire' => $expire,
						'path' => $path
					);
					
					if ( is_file( $tagFile ) ) {
						$data = unserialize( file_get_contents( $tagFile ) );
						$data[ $cacheID ] = $tagInfo;
						file_put_contents( $tagFile, serialize($data), $this->options['lock']?LOCK_EX:0 );
					} else {
						$data = array( $cacheID => $tagInfo );
						file_put_contents( $tagFile, serialize($data), $this->options['lock']?LOCK_EX:0 );
					}
					
					$tagsData[] = $tagInfo;
				}
			}
			
			$cacheMeta = $path.K_CACHE_SIMPLE_CACHE_META;
			file_put_contents( $cacheMeta, serialize( array( 'expire'=>$expire, 'tags'=>$tagsData ) ), $this->options['lock']?LOCK_EX:0 );
		}		
	}
	
	public function load( $cacheID ) {					
		// get sequence path, but no create directories see _sequensePathCreate "false"
		$path = $this->dmCacheSimpleIDs . $this->_sequensePathCreate( $cacheID, $this->options['cache_dir'] . K_CACHE_SIMPLE_IDS, false );
		
		$cacheMeta = $path.K_CACHE_SIMPLE_CACHE_META;
		$cacheFile = $path.K_CACHE_SIMPLE_CACHE_OBJECT;
		
		if ( !is_file($cacheFile) || !is_file($cacheMeta) ) {
			$this->clear( $cacheID );
			return FALSE;
		}			
		
		$meta = unserialize( file_get_contents( $cacheMeta ) );
		
		if ( $meta['expire'] < time() ) {
			$this->clear( $cacheID );
			return FALSE;
		}
		
		return unserialize( file_get_contents( $cacheFile ) );
	}
	
	public function loadRender( $cacheID, $drawNow = false ) {
		// get sequence path, but no create directories see _sequensePathCreate "false"
		$path = $this->dmCacheSimpleIDs . $this->_sequensePathCreate( $cacheID, $this->options['cache_dir'] . K_CACHE_SIMPLE_IDS, false );
		
		$cacheMeta = $path.K_CACHE_SIMPLE_CACHE_META;
		$cacheFile = $path.K_CACHE_SIMPLE_CACHE_OBJECT;
		
		if ( !is_file($cacheFile) || !is_file($cacheMeta) ) {
			$this->clear( $cacheID );
			return FALSE;
		}			
		
		$meta = unserialize( file_get_contents( $cacheMeta ) );
		
		if ( $meta['expire'] < time() ) {
			$this->clear( $cacheID );
			return FALSE;
		}
		
		if ( $drawNow ) {
			readfile( $cacheFile );
			return TRUE;
		} else {
			return file_get_contents( $cacheFile );
		}
	}
	
	public function saveRender( $cacheID, $data, $tags = array(), $lifetime = 0 ) {
		if (is_dir($this->options['cache_dir'])) {
			
			if ( $lifetime == 0 ) {
				$lifetime = $this->options['lifetime'];
			}
			
			$path = $this->dmCacheSimpleIDs . $this->_sequensePathCreate( $cacheID, $this->dmCacheSimpleIDs );	
			$cacheFile = $path.K_CACHE_SIMPLE_CACHE_OBJECT;
			
			file_put_contents( $cacheFile, $data, $this->options['lock']?LOCK_EX:0 );
			
			$expire = time()+$lifetime;
			$tagsData = array();
						
			if ( count($tags) ) {
				$tagsRoot = $this->dmCacheSimpleTags;	
				foreach($tags as $tagName) {
					
					if ( !is_dir($tagsRoot) ) {
						mkdir( $tagsRoot, $this->options[ 'chmod' ] );
						chmod( $tagsRoot, $this->options[ 'chmod' ] );
					}
					
					$tagPath = $this->_sequensePathCreate( $tagName, $tagsRoot );
					$tagFile = $tagsRoot.$tagPath.K_CACHE_SIMPLE_TAG_OBJECT;

					$tagInfo = array(
						'tag' => $tagName,
						'expire' => $expire,
						'path' => $path
					);
					
					if ( is_file( $tagFile ) ) {
						$data = unserialize( file_get_contents( $tagFile ) );
						$data[ $cacheID ] = $tagInfo;
						file_put_contents( $tagFile, serialize($data), $this->options['lock']?LOCK_EX:0 );
					} else {
						$data = array( $cacheID => $tagInfo );
						file_put_contents( $tagFile, serialize($data), $this->options['lock']?LOCK_EX:0 );
					}
					
					$tagsData[] = $tagInfo;
				}
			}
			
			$cacheMeta = $path.K_CACHE_SIMPLE_CACHE_META;
			file_put_contents( $cacheMeta, serialize( array( 'expire'=>$expire, 'tags'=>$tagsData ) ), $this->options['lock']?LOCK_EX:0 );
		}	
	}
	
	public function clear( $cacheID, $removeTags = true ) {
		if (is_dir($this->options['cache_dir'])) {
                        
			// get sequence path, but no create directories see _sequensePathCreate "false"
			$path = $this->dmCacheSimpleIDs . $this->_sequensePathCreate( $cacheID, $this->options['cache_dir'] . K_CACHE_SIMPLE_IDS, false );
			
                        $cacheMeta = $path.K_CACHE_SIMPLE_CACHE_META;
			
			if ( is_file($cacheMeta) ) {

				if ( $removeTags ) {
					$meta = unserialize( file_get_contents( $cacheMeta ) );
					if ( count($meta) ) {
						foreach($meta['tags'] as $tag) {
							if ( is_file($tag['path']) ) {
								@unlink( $tag['path'] );
							}
						}
					}
				}
				
				@unlink($cacheMeta);
			}
                       
			$cacheFile = $path.K_CACHE_SIMPLE_CACHE_OBJECT;
            //clearstatcache( false, $cacheFile );
			if ( is_file($cacheFile) && file_exists($cacheFile) ) {
				@unlink($cacheFile);
			}
		}
	}
	
	public function clearByTags( $tags ) {
		if (count($tags)) {
			$tagsRoot = $this->dmCacheSimpleTags;
			foreach( $tags as $tagName ) {
				$tagPath = $this->_sequensePathCreate( $tagName, $tagsRoot );
				$tagFile = $tagsRoot.$tagPath.K_CACHE_SIMPLE_TAG_OBJECT;
				if ( is_file($tagFile) ) {
					$tagsData = unserialize(file_get_contents($tagFile));
					if (count($tagsData)) {
						foreach($tagsData as $cacheID => $tagInfo) {
							$metaFile = $tagInfo['path'].K_CACHE_SIMPLE_CACHE_META;
							if ( is_file($metaFile) ) {
								@unlink($metaFile);
							}
							$cacheFile = $tagInfo['path'].K_CACHE_SIMPLE_CACHE_OBJECT;
							if ( is_file($cacheFile) ) {							
								@unlink($cacheFile);
							}
						}
					}
				}
				if ( is_file($tagFile) ) {	
					@unlink($tagFile);
				}
			}
		}
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
	// Allias clear
	public function remove( $cacheID ) {
		$this->clear( $cacheID );
	}
}

?>