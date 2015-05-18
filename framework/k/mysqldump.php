<?php 

class K_MysqlDump{	
   
      private $options = array(
      
                        'host' => 'localhost',
                        'user' => 'root',
                        'password' => '',
                        'database' => '',
                        'dir'=>'',
                        'insertRecordsCount'=>50
                        
					);
  
      public function __construct( $options = array()){
        
          $this->settings($options);
        
      }
      
      public function settings( $options = array()){
        
          $this->options['dir'] = ROOT_PATH.'/_backup/_dump';
          $this->options = array_merge($this->options, $options);
       
      }
  
      public function dump($dumpName = 'mysqldump', $gzip= true, $stream = false){
                     
            $link = mysql_connect($this->options['host'], $this->options['user'], $this->options['password']) or die( "Сервер базы данных не доступен" );
            $db = mysql_select_db($this->options['database']) or die( "База данных не доступна" );
            $res = mysql_query("SHOW TABLES") or die( "Ошибка при выполнении запроса: ".mysql_error() );
            $fp = fopen( $this->options['dir']."/".$dumpName, "w" );
            
            while( $table = mysql_fetch_row($res) )
            {
            $query="";
                if ($fp)
                {
            		$res1 = mysql_query("SHOW CREATE TABLE ".$table[0]);
            		$row1=mysql_fetch_row($res1);
            		$query="\nDROP TABLE IF EXISTS `".$table[0]."`;\n".$row1[1].";\n";
                    fwrite($fp, $query); $query="";
                    $r_ins = mysql_query('SELECT * FROM `'.$table[0].'`') or die("Ошибка при выполнении запроса: ".mysql_error());
            		if(mysql_num_rows($r_ins)>0){
            		$query_ins = "\nINSERT INTO `".$table[0]."` VALUES ";
            		fwrite($fp, $query_ins);
            		$i=1;
                    while( $row = mysql_fetch_row($r_ins) )
                    { $query="";
                        foreach ( $row as $field )
                        {
                            if ( is_null($field) )$field = "NULL";
                            else $field = "'".mysql_escape_string( $field )."'";
                            if ( $query == "" ) $query = $field;
                            else $query = $query.', '.$field;
                        }
            			if($i>$this->options['insertRecordsCount']){
            							$query_ins = ";\nINSERT INTO `".$table[0]."` VALUES ";
            							fwrite($fp, $query_ins);
            							$i=1;
            							}
                        if($i==1){$q="(".$query.")";} else $q=",(".$query.")";
            			fwrite($fp, $q); $i++;
                    }
                    fwrite($fp, ";\n");
            	}
                }
            } fclose ($fp);
            
            if($gzip||$stream){ $data=file_get_contents($this->options['dir']."/".$dumpName);
            $ofdot=".sql";
            if($gzip){
            	$data = gzencode($data, 9);
            	unlink($this->options['dir']."/".$dumpName);
            	$ofdot=".gz";
            }
            
            if($stream){
            		header('Content-Disposition: attachment; filename='.$dumpName.$ofdot);
            		if($gzip) header('Content-type: application/x-gzip'); else header('Content-type: text/plain');
            		header("Expires: 0");
            		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            		header("Pragma: public");
            	    return $data;
                  
            }else{
            		$fp = fopen($this->options['dir']."/".$dumpName.$ofdot, "w");
            		fwrite($fp, $data);
            		fclose($fp);
            	}
            }
        }    
}

?>