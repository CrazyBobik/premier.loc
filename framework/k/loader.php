<?php

/**
 * DM libraries loader
 */

class K_Loader {
    
	static function load( $path, $prePath = null ) {                
                if ( !empty($prePath) ) {
                    if ( file_exists($prePath.'/'.$path.'.php') ) {
                        require_once($prePath.'/'.$path.'.php');
                        return true;
                    }
                } else {
                    if ( file_exists($path.'.php') ) {                        
                        require_once($path.'.php');
                        return true;
                    }
                }
                if ( file_exists(K_PATH.'/'.$path.'.php') ) {                    
                    require_once(K_PATH.'/'.$path.'.php');
                    return true;
		}
		throw new Exception('Include file '.$path.' not found. '.$prePath.'/'.$path.'.php');
		return false;
	}
        
	public static function auto_load($class)
	{	
	    // echo  $class."\n";
       
            try
            {
                // Transform the class name into a path
               $file = str_replace('_', '/', strtolower($class));
				
                if ($path = self::find_file($file))
                {
                 
                    // Load the class file
                    require $path;

                    // Class has been found
                    return TRUE;
                }

                // Class is not in the filesystem
                return FALSE;
            }
            catch (Exception $e)
            {
                throw(new Exception($e->getMessage()));
            }
	}
        
        public static function find_file($path)
        {
            $resultPath = false;
            
			if (file_exists(APP_PATH.'/'.$path.'.php'))
			{
				$resultPath = APP_PATH.'/'.$path.'.php';
			}
            elseif (file_exists(PLATFORM_PATH.'/'.$path.'.php') && !$resultPath)
            {  
                $resultPath = PLATFORM_PATH.'/'.$path.'.php';
            }
            elseif (file_exists($path.'.php') && !$resultPath)
            {                        
                $resultPath = $path.'.php';
            } 
            else
            {
                throw new Exception('Include file '.($resultPath ? $resultPath : $path).' not found.');
                return false;
            }
            
            return $resultPath;
        }
}

?>