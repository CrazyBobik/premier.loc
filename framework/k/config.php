<?php 

class K_Config {
    
    public $configArray = array(); 
    
	public static function loadIni( $path ) {
	   
		if ( !is_file($path) ) return FALSE;
		$data = array();
		$iniData = parse_ini_file( $path, true );
		if ( is_array($iniData) && count($iniData) ) {
			self::_circle($iniData, $data);
		}
		unset($iniData);
		return $data;
        
	}
  
	protected static function _circle( &$iniData, &$data ) {
	   
		foreach( $iniData as $key => &$value ) {
			if ( is_string($value) ) {
				$key = explode('.', $key);
				$v = &$data;
				if ( is_array($key) && count($key) ) {					
					foreach($key as &$subKey) {
						$v = &$v[ $subKey ];
					}
				} else {
					$v = &$v[ $key ];
				}
				$v = $value;
			} elseif ( is_array($value) ) {
				self::_circle( $iniData[$key], $data[$key] );
			}
		}
        
	}
       
	public function __construct( $configArray ) {
  	 
	   $this->configArray = $configArray;
      
	}
    
   	public function validate(){
  	       
       $validate = array();
       
       foreach($this->configArray['fields'] as $k=>$v){
     
            if(isset($v['validate'])){
                
                $validate[$k] = $v['validate'];
                
	        }
               
       } 
       
       return $validate;
      
	}
    
   	public function lables(){
  	 
       $lables = array();
       
       foreach($this->configArray['fields'] as $k=>$v){
     
            $lables[$k] = $v['lable'];
	          
       } 
       
       return $lables;       
      
	}
       
   	public function whereSets(){
  	  
       return $this->configArray;            
      
	}   
             
   	public function data($post){
  
       $data = array();
       
       foreach($this->configArray['fields'] as $k=>$v){
       
         if($k==$this->configArray['primary']){
            
             $data[$k] = intval($post[$k]);
            
         }elseif(isset($v['validate'])){
            
               if(in_array ('int', $v['validate'], true)){
                
                  $data[$k] = k_string::treat($post[$k], 12);
               
               }elseif(isset($v['validate']['maxlen'])){
                
                  $data[$k] = k_string::treat($post[$k], $v['validate']['maxlen']+1);
                
               }else{
                
                  $data[$k] = k_string::treat($post[$k], $this->configArray['fieldsMaxLen']+1);
                
               }
               
          }else{
		  
		    $data[$k] = $post[$k];
	  
		  }
   	          
       } 
       
       return $data;       
      
	}
    
    public function fieldConfig($name){
    
       return $this->configArray['fields'][$name];       
      
	}
     
   	public function primary(){
    
       return $this->configArray['primary'];       
      
	}
        
  	public static function load( $confgArray ) {
  	 
	   return new K_Config($confgArray);
     
	}
    
}
	