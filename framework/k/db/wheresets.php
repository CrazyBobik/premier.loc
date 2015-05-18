<?php

class K_Db_WhereSets {
     
    /** Функция возвращает сформированный из поста массив where
      *   
      * 
      * 
    */
    
    public $data = array();
    
    public $config = array();
    
    private $_whereSet = array();
         
    public function likes($fields, $pts) {
                      
        foreach($fields as $k=>$v){
            
             if(isset($pts[$k]) && !empty($pts[$k])){
              
                   if($pts[$k] == "нет"){
                  
                      $this -> _whereSet[] = $v.' IS NULL ';
                    
                    }else{
                     
                      $this -> _whereSet[] = $v.' LIKE '.K_Db_Quote::quote($pts[$k].'%');
                     
                    };
                
             }
             
        }
        
        return $this;
        
     }  
     
     public function like($field, $value){
            
             if(!empty($value)){
              
                   if($value == "нет"){
                  
                      $this ->_whereSet[] = $field.' IS NULL ';
                    
                    }else{
                     
                      $this ->_whereSet[] = $field.' LIKE '.K_Db_Quote::quote($value.'%');
                     
                    };
                
             }
             
        return $this;
        
     }       
     
     public function add($field, $value){
            
             if(!empty($value)){
              
                   if($value == "нет"){
                  
                      $this ->_whereSet[] = $field.' is NULL ';
                    
                    }else{
                     
                      $this ->_whereSet[] = $field.' = '.K_Db_Quote::quote($value);
                     
                    };
                
             }
             
        return $this;
        
     }  
     
     /* type= |timestamp
         
     */
     
     public function beetwen($field, $start, $stop, $type = false) {
        
            if($type=='TIMESTAMP'){
                
               $field = 'UNIX_TIMESTAMP('.$field.')';
                
            }
       
            if (isset($start) && $stop) {
                if ($start > $stop) {
                    
                    $this->_whereSet[] ="$field >= ".K_Db_Quote::quote($start);
                    
                } else {
                    
                    $this->_whereSet[] ="($field BETWEEN  ".K_Db_Quote::quote($start)." AND ".K_Db_Quote::quote($stop).")";
                    
                }
            } elseif ($start) {
                
                $this->_whereSet[] ="$field >= ".K_Db_Quote::quote($start);
                
            } elseif ($stop) {
                
                $this->_whereSet[] ="$field <= ".K_Db_Quote::quote($stop);
                
            }
           
        return $this;
        
     }  
     
     public function fromConfig($settings){
         
          foreach($settings['fields'] as $k => $v){ //set
           
               if(isset($v['set'])){
                    
                    if($v['set']=='like'){
                         
                        $this->like( $settings['alias'].'.'.$k, $this->data[$k]);
                        
                    }elseif($v['set']=='add'){
                        
                        $this->add( $settings['alias'].'.'.$k, $this->data[$k]);;
                        
                    }elseif($v['set']=='between'){
                        
                        $this->beetwen( $settings['alias'].'.'.$k, $this->data['start-'.$k], $this->data['stop-'.$k], $v['type']);
                         
                    }
                    
               }
               
           }
           
           return $this->_whereSet;
     }  
     

     public function __construct($data) {
      
        $this->data = $data;
        
     }  
     
     public function get($data) {
      
        return new K_Db_WhereSets($data);
        
     }  


	
}