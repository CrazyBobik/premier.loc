<?php 

class K_File{
    
    function safeFilename($name) { 
		$except = array('\\', '/', ':', '*', '?', '"', '<', '>', '|'); 
		return str_replace($except, '', $name); 
    } 
    
    
    /** читает все файлы из директории и возвращяет в виде списка
    *
    */
    
    public static function rdir($dir){
   
           if(!is_dir($dir)){
               
               return false;
           }
        
           $filesNames = array();
        
           if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) { 
					if($file[0]!='.'){	
						if ($file != "." && $file != "..") { 
							$filesNames[] = $file;
						} 
					}
                }
                closedir($handle); 
           }
           
           return  $filesNames;
           
    }
    
    /** Создаёт новый файл при force = true создаёт каталоги по пути и перезаписывает файл если он существует  
    * 
    */    
    
    public static function create($path, $force = false, $handle = false){
         
           if( !$force && file_exists($path) ){
                   return false;
           }
           
           $pathArray = pathinfo($path);
             
           if($force){
            
               if(!self::mkdirRecursive( $pathArray['dirname'])){
                 
                   return false;
                
               };  
                 
           }      
       
           $des = fopen($path, "w");
           
           if($handle){
            
             return  $des;
            
           }; 
           
           fclose($path);        
           
           return  true;
           
    }
    
    public static function mkdirRecursive($dirName, $rights = 0777){
        
        $dirs = spliti("/[\/\\\\]/", $dirName, PREG_SPLIT_NO_EMPTY);
        
        $dir='';
        
        foreach ($dirs as $part) {
            
            $dir.=$part.'/';
            
            if (!is_dir($dir) && strlen($dir)>0){
                
                 if(!mkdir($dir, $rights)){
                    
                    return false;
                    
                 };
                  
            }
              
        }
        
        return true;
        
    }
        
}






?>