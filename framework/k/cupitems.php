<?php 

/**
 * Class K_CupItems - Работа с итемами без участия дерева
 * <example>
 
 * </example>
 */

class K_CupItems {
    
    /**
     * Возвращяет итемы опрделённого типа 
     * 
     * @param $opt - либо массив, либо id искомого итема 
     * @param $type - тип, искомого итема 
     * @param $stripFields - удалять название таблицы из в выдаче  
     * 
     * Возможна форма записи аргументов как массив, оставшиеся аргументы не указываються
     * 
     * $opt=array(
     * 'type'= 'manager',
     * 'offset'= fasle,
     * 'limit'= 2,
     * 'random'= true
     * 'order'= 'date_publication'
     * )
     *  
     */
        
    public static function getItems($opt,$type,$stripFields=true)
    {
    // для поддержки сокращённого типа вызова   
      if(!is_array($opt)){
        $opt = array('id'     => $opt,
                   'type'   => $type,
                   'offset' => false,
                   'limit'  => false,
                   'random' => false, 
                   'order' => false,
                   'strip'  =>  $stripFields,
                  
                   );
      }else{ 
       
       $opt=array_merge(array('offset' => false,
                                   'limit'  => false,
                                   'random' => false,
                                   'strip'  => true,
                                   'joins'  => false,
                                   'where'  => false
                                  ),$opt
                        ); 
       } 
       
       
       
        //var_dump($opt); 
       $offset=$opt['offset'];
       $query = new K_Db_Query();
       if($opt['random']){
            $result = $query->q('SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM `type_'.$opt['type'].'` ');
            
            if(!$opt['limit']){
              $opt['limit']=1;  
            }   
       }
       $whereStr='';
       
       if($opt['id']){
         $whereStr ='WHERE ty.type_'.$opt['type'].'_id = '.$opt['id']; 
       }
       else if($opt['where']){
         $whereStr='WHERE '.$opt['where'];
       }
       
       if($opt['limit']){
            $limitStr=' LIMIT '.($offset ? $offset.',' :'').$opt['limit'];
       }
       
       if($opt['order']){
            $orederStr = ' ORDER BY `type_'.$opt['type'].'_'.$opt['order'].' ';
       }
      
    /// echo ('SELECT ty.* FROM `type_'.$opt['type'].'` ty '.$whereStr.$limitStr);
     
        $result = $query->q('SELECT ty.* FROM `type_'.$opt['type'].'` ty '.$whereStr.$orederStr.$limitStr);
  
 
       if ($opt['strip']){
        
        
          $rData = self::stripTypeFields($result,$opt['type']);
          
        }else{
            
          $rData =$result;  
          
        }     
        // var_dump($result);

        return $rData;
   } 
   
    /**
     * Функция удаляет префикс таблицы у столбца    
     * 
     **/
     
   public static function stripFieldsArr($data, $type, $htmlchars = false){
    
    $rData=array();
    
    if (!count($data)){
        return $rData;         
    }
    
    if($data instanceof K_Db_Row){
        $data=$data->toArray();
    }
			foreach ($data as $typeField => $typeValue)
			{
			 
                 if($htmlchars){
                    $rData[str_replace($type.'_', '', $typeField)] = htmlspecialchars($typeValue);
                 }else{
    				$rData[str_replace($type.'_', '', $typeField)] = $typeValue;
                 }
			}
  return $rData;         
  }    
      
    /**
     * Функция удаляет префикс таблицы у столбца    
     * 
     **/
     
   public static function stripFields($arrs, $type){
    $rData=array();
    if($size=count($arrs))
	for ($i = 0; $i < $size; $i++)
		{
			$data[$i] = $arrs[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace($type.'_', '', $typeField)] = $typeValue;
			}
		} 
        
    return $rData;         
  }    
   
   
    /**
     * Функция удаляет префикс названия таблицы типа у столбца    
     * 
     **/
     
   public static function stripTypeFields($arrs, $type){
    $rData=array();
	for ($i = 0; $i < sizeof($arrs); $i++)
		{
			$data[$i] = $arrs[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
			}
		} 
        
    return $rData;         
  }    
   
}
?>
