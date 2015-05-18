<?php
class K_AdminFunc {     
     
     /** Функция возвращает сформированный из поста массив where
      *   
      * 
      * 
      */
         
    public static function whereLike($fields, $pts) {
                      
        foreach($fields as $k=>$v){
            
             if(isset($pts[$k]) && !empty($pts[$k])){
              
                   if($_POST[$k] == "нет"){
                  
                       $where[] = $v.' IS NULL ';
                    
                    }else{
                     
                       $where[] = $v.' LIKE '.K_Db_Quote::quote($_POST[$k].'%');
                     
                    };
                
             }
             
        }
        
        return $where;
        
     }  
}        
?>            