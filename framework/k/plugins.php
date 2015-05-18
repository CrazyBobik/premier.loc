<?php

/**
 * Plugin controller
 * Can use platform hook or external
 * 
 * Default hooks -> params => description
 *      Call in controller:
 *          controller.viewCreate           -> {controller:<instance to controller object>, view:<instance to controller->view object>} => call on view create
 *          controller.beforeInit           -> {controller:<instance to controller object>} => call before controller onInit function
 *          controller.afterInit            -> {controller:<instance to controller object>}
 *          controller.beforeRender         -> {controller:<instance to controller object>}
 *          controller.afterRender          -> {controller:<instance to controller object>}
 *          controller.beforeRenderComplete -> {controller:<instance to controller object>}
 *          controller.afterRenderComplete  -> {controller:<instance to controller object>}
 *          controller.beforeDestroy        -> {controller:<instance to controller object>}
 *          controller.afterDestroy         -> {controller:<instance to controller object>}
 */

class K_Plugins {
    private static $plugins = array();
    
    private static $hooks = array();
    
    private static $directories = array();
    
    private function __construct() {}
    
    public static function load( $pluginName, $pluginPath = null ) {
        if ( is_string($pluginName) ) {
            self::loadPlugin($pluginName, $pluginPath);
        } elseif ( is_array($pluginName) && count($pluginName) ) {
            foreach( $pluginName as $pname ) {
                self::loadPlugin($pname, $pluginPath);
            }
        }        
    }
    
    public static function get( $pluginName ) {
        $pluginShortName = strtolower(trim($pluginName));
        
        if ( array_key_exists( $pluginShortName, self::$plugins ) ) {
            return self::$plugins[ $pluginShortName ];
        }
        
        return null;
    }
    
    private static function loadPlugin( $pluginName, $pluginPath = null ) {
        $pluginShortName = strtolower(trim($pluginName));
        
        if ( array_key_exists( $pluginShortName, self::$plugins ) ) {
            return;
        }
        
        $path = null;
        
        if ( !empty($pluginPath) ) {
            $tmpPath = realpath( $pluginPath ).$pluginShortName.'Plugin.php';
            if ( file_exists($tmpPath) ) {
                $path = $tmpPath;
            }
        }
         
        if ( empty($path) && count(self::$directories) ) {
            // array_reverse used for change directory priority to LIFO
            foreach( array_reverse(self::$directories) as $directory ) {
                $tmpPath = realpath( $directory ).'/'.$pluginShortName.'Plugin.php';
                if ( file_exists($tmpPath) ) {
                    $path = $tmpPath;
                    break;
                }
            }
        }
               
        if ( !empty($path) ) {
            require_once $path;
            $plugin = null;
            eval( '$plugin = new '.$pluginShortName.'Plugin();' );
            if ( $plugin ) {
                self::$plugins[ $pluginShortName ] = &$plugin;
                if ( method_exists( $plugin, 'onInit') ) {
                    $plugin->onInit();
                }
            }
        }
    }
    
    public static function addHook( $hookName, $plugin, $method ) {  
        $hookName = strtolower(trim($hookName));
        
        if ( !isset(self::$hooks[ $hookName ]) ) {
            self::$hooks[ $hookName ] = array();
        }
        
        self::$hooks[ $hookName ][] = array(
            'plugin' => $plugin,
            'method' => $method
        );
    }
    
    public static function callHook( $hookName, $hookParams = null ) {        
        $hookName = strtolower(trim($hookName));
        if ( !empty( self::$hooks ) && is_array( self::$hooks ) && isset( self::$hooks[ $hookName ] ) ) {
            foreach( self::$hooks[ $hookName ] as $hookInfo ) {       
                if ( array_key_exists( $hookInfo['plugin'], self::$plugins ) ) {    
                    $pluginObject = self::$plugins[ $hookInfo['plugin'] ];
                    if ( method_exists( $pluginObject, $hookInfo['method'] ) ) {
                        $method = $hookInfo['method'];
                        $pluginObject->$method( $hookParams );
                    }
                }
            }
        }
    }
    
    public static function addDirectory( $path ) {
        if ( is_dir(realpath($path))) {
            self::$directories[] = realpath($path);
        }
    }
}

?>
