<?php

/**
 * @package    DM
 * @category   Helpers
 */
class K_Inet{
  
  private static $currencyArr = FALSE;  
    
  public static function grabFile($loc) {
        $ch = curl_init($loc);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        $data = curl_exec($ch);

        if (! $data || curl_errno($ch) != 0 || curl_getinfo($ch, CURLINFO_HTTP_CODE) > 403) {
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $data;
    }
        
    public function grabFileRep($url, $countRepeat = 5, $sleep = 5, $say = false){
         $repeat = 0;
     
          do{ 
              $repeat++;
      
            if($repeat > $countRepeat){
                break;
            }
             sleep($sleep);
          } while (0);
           
          if($repeat>=$countRepeat){
            if($say){
               echo "Не удалось загрузить файл по ссылке ".$url." после  ".$repeat." попыток";
              
            }
            return false;
          }   
  
         return $data;  
   }
    
   public static function saveFileRep($url, $path, $countRepeat = 5, $sleep = 5, $say = false){
    
        $repeat = 0;
    
        do{
          $repeat++;
           
          if(($repeat > $countRepeat || self::saveFile($url ,$path)))  {
            break;
          }
          
          sleep($sleep);
          
        } while (0);
       
       if($repeat>=$countRepeat){
          if($say){
               echo "Не удалось загрузить файл по ссылке ".$url." после  ".$repeat." попыток";
               K_cli::nbr();
           }
           
           return false;
       }   
        
       return true; 
   }

    

    public static function saveFile($loc, $dsc, $zip = false) {
        if (function_exists(curl_init)) {

            if (file_exists($dsc)) {
                unlink($dsc);
            }
            $f1 = @fopen($dsc, "w");

            $ch = curl_init($loc);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_FILE, $f1);

            if ($zip) {
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip , deflate');
            }

            if (curl_errno($ch) != 0 || curl_getinfo($ch, CURLINFO_HTTP_CODE) > 403) {
                curl_close($ch);
                fclose($f1);
                return false;
            }

            curl_exec($ch);
            curl_close($ch);
            fflush($f1);
            fclose($f1);
            return true;
        }
    }
    // получение валюты
     public static function getCurrency( $char3 = "USD", $url='http://bank-ua.com/export/currrate.xml'){
  
		if (self::$currencyArr==FALSE) {
		
			$cacheManager = K_Registry::get('cacheManager');
			$cache24h =  K_Cache_Manager::get('24h');
	  
			if (!$cache24h->test('CURS')){
				
				$currencyXml = simplexml_load_file($url);
				
				$currencyArr = json_decode(json_encode($currencyXml), TRUE); 
				
				$cache24h->save('CURS',	$currencyArr);
				
			} else {
			
					$currencyArr = $cache24h->load('CURS');
				
			}
		
			self::$currencyArr =$currencyArr;
		}
	
		// все хорошо, можно работать дальше -
		// в XML-данных нет ошибки
			
		foreach(self::$currencyArr['item'] as $item){
								
			if($item['char3'] == $char3){
			
				$result = round($item['rate']/100, 2);
				break;
				
			}
			   
		}
		
        return $result;
    }
    
    
}
