<?php 

/**
 * Class ViewHelper
 * Singleton
 */

class K_ViewHelper {
	protected static $instance = null;
	protected $dirs = array();
	
	protected function __construct() {
        $this->addDirectory( K_PATH.'/helpers' );
		self::$instance = $this;
	}
	
	public static function get() {
		if ( self::$instance ) {
			return self::$instance;
		}
		self::$instance = new K_ViewHelper();
		return self::$instance;
	}
	
	public function __destruct() {
		self::$instance = null;
	}
	
	public function addDirectory( $path ) {
        if ( is_dir($path) ) {
			$this->dirs[] = $path;
		}
	}
	
	public function loadHelper( &$view, $helperName ) {
	   
       //var_dump($this->dirs);
		foreach ($this->dirs as $dirName) {
                        if ( is_dir($dirName) && is_file( realpath($dirName).'/'.$helperName.'.php' ) ) {
				require_once realpath($dirName).'/'.$helperName.'.php';
				if ( !method_exists( $view, $helperName ) ) {
					$helperObj = $helperName.'Helper';
					$view->$helperName = new $helperObj();
				} else {
					throw new Exception('Cant create helper '.$helperName.', View`s method already exists.');
				}
			}
		}
	}
}

?>