<?php 

/**
 * Class K_Capture
 * <example>
 * K_Capture::start();
 * ... output ...
 * $data = K_Capture::end();
 * or 
 * $data = K_Capture::endAndFlush();
 * </example>
 */

class K_Capture {	
	protected function __construct() {}

	public static function start() {
		ob_start();
	}
	
	public static function end() {
		$content = ob_get_contents();
    	ob_end_clean();
    	return $content;
	}
	
	public static function endAndFlush() {
		$content = ob_get_contents();
		ob_flush();
    	flush(); 
		return $content;
	}
	
	public static function flush() {
		ob_flush();
    	flush();
	}
    
   	public static function chunk($chunk) {
   	    
   	    self::start();
           require_once(CHUNK_PATH.'/'.$chunk.'.php');
        return self::end();
    
	}
    
}

?>