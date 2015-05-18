<?php
  
   if($_GET[AllConfig::$mysqlDump['secureTokenArg']] != AllConfig::$mysqlDump['secureToken']){
        
        echo "TOKEN ERROR";
        exit(); 
        
    }
    
    // объединяем массив конфига базы данных и массив дополнительных настроек
     
    $options = array_merge( AllConfig::$mysqBDConf, array(// дополнительные настройки
    
                                                            'insertRecordsCount' => AllConfig::$mysqlDump['insertRecordsCount'] 
                                                 
                                                         )
                            );
   
     // удаляем старые бекапы
    $dumps = K_File::rdir(ROOT_PATH.'/_backup/_dump');
 
    $dumpName = AllConfig::$mysqBDConf['database'].'_'.date('d-m-Y').'_'.date('H=i=s');
  
     foreach($dumps as $v){
        
          $date = K_Date::dateParse($dumpName);
       
          if((time()-$date['ts'])>(60*60*24*10)){
            
                unlink(ROOT_PATH.'/_backup/_dump'.'/'.$v);
    
          }
     }
     //  
     
     
      
    $dump = new K_MysqlDump($options);
    
    $dump->dump($dumpName , $gzip = true, $stream = false);
    
    // не забываем выключить главный лайаут
    $this->disableLayout = true;
    exit(); 
       
?>