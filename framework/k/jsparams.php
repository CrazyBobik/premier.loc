<?php 

/**
 * Class JsParams
 * 
 * @ output java-script variables  in to page 
 * 
 * <example>
 * $model = new K_Db_Model();
 * $string = $model->name;
 * K_Debug::get()->Add( true );
 * K_Debug::get()->dump( $model );
 * K_Debug::get()->dump( $string );
 * K_Debug::get()->addMessage( '--- complete ---' );
 * K_Debug::get()->printAll();
 * K_Debug::get()->enable( false );
 * </example>
 */

class K_JsParams {

	private static $globalOptions = array();
	
    public static function add($key, $value){
        
           self::$globalOptions[$key] = $value;
           
    }
    
    public static function output(){
        
           if(count($globalOptions)){
                     return '<script type="text/javascript">var globalOptions = '.json_encode(self::$globalOptions).'</script>';
           }  
        
    }

}
?>