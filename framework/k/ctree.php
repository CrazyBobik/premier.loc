<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class K_cTree {
	
	private $type; // определят тип ноды в деревер, от типа зависит из какой таблицы будет выбираться информация для нод
	
	public function __construct($type = false)
	{
		if (self::typeExists($type))
		{
			$this->type = $type;
		}
		else
		{
			throw new Exception('Type "'.$type.'" is not exists!');
		}
	}
    
    /**
     * Ищет ноду по ID и отдаёт инормацию по ней
     * 
     * 
     * 
     */
  	public function get($nodeId)
	{
		$result   = array();
		$nodeData = array();
		$typeData = array();
		$data     = array();
		
		$nodeData = K_Tree::getNode($nodeId);
		$typeModelName = 'Type_Model_'.ucfirst($this->type);
		$typeModel = new $typeModelName();
		
		if (is_int($nodeId))
		{
			$typeData = $typeModel->select()->where('`type_'.$this->type.'_id`='.$nodeId)->fetchRow();
		}
		else
		{
			$typeData = $typeModel->select()->where('`type_'.$this->type.'_id`='.$nodeData['tree_id'])->fetchRow();
		}
		
		$typeData = $typeData->toArray();
		
		foreach ($typeData as $typeField => $typeValue)
		{
			$data[str_replace('type_'.$this->type.'_', '', $typeField)] = $typeValue;
		}
		
		$result = array_merge($nodeData, $data);
		
		return $result;
	}
	
      /**
     * Ищет ноду по строке и отдаёт инормацию по ней
     * 
     * 
     * 
     */
    
	public function search($queryString, $nodePid = 1, $limit = false)
	{
		if (!is_string($queryString))
		{
			throw new Exception('cTree::search parameter must be string!');
		}
		
		$parentNodeData = K_Tree::getNode($nodePid);
		
		$result = array();
		$data   = array();
		$rData  = array();
		
		$query = new K_Db_Query();
		$result = $query->q('SELECT `tr`.*, `ty`.* FROM `tree` AS `tr`, `type_'.$this->type.'` AS `ty` WHERE `tr`.`tree_id`=`ty`.`type_'.$this->type.'_id` AND `tr`.`tree_lkey`>'.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<'.$parentNodeData['tree_rkey'].($queryString ? ' AND '.$queryString : '').' ORDER BY `tr`.`tree_lkey` ASC '.($limit ? ' LIMIT '.(int)$limit : ''));
		
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$this->type.'_', '', $typeField)] = $typeValue;
			}
		}
		
		return $rData;
	}
    
    /**
     * Возвращяет итем определённого типа по id            
     */
    
    public function getItem($id, $stripFields=true)
    {
        
       	$query = new K_Db_Query();
        $result = $query->q('SELECT ty.* FROM `type_'.$this->type.'` ty WHERE type_'.$this->type.'_id="'.$id.'" ;');
 
        if ($stripFields){
          	for ($i = 0; $i < sizeof($result); $i++)
    		{
    			$data[$i] = $result[$i]->toArray();
    			
    			foreach ($data[$i] as $typeField => $typeValue)
    			{
    				$rData[str_replace('type_'.$this->type.'_', '', $typeField)] = $typeValue;
    			}
    		} 
        }else{
            
         $rData =$result;  
            
        }
        
        return $rData;
    }
     
    /**
     * Возвращяет итемы опрделённого типа  
     * $opt=array(
     * 'type'= 'manager',
     * 'offset'= fasle,
     * 'limit'= 2,
     * 'random'= treu
     * )
     * 
     * 
     */
        
    public static function getItems($opt)
    {
       $offset=$opt['offset'];
      	$query = new K_Db_Query();
       if($opt['random']){
            $result = $query->q('SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM `type_'.$opt['type'].'` ');
            
            if(!$opt['limit']){
              $opt['limit']=1;  
            }   
       }
       
       if($opt['limit']){
            $limitStr=' LIMIT '.($offset ? $offset.',' :'').$opt['limit'];
       }
     
        $result = $query->q('SELECT ty.* FROM `type_'.$opt['type'].'` ty '.$limitStr);
 
       	for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$opt['type'].'_', '', $typeField)] = $typeValue;
			}
		} 
        
        return $rData;
    }
    
    
    /**
     * Получает дочернии ноды определённого(заданного при создании класса) типа вместе c инфрмацией для этих нод
     * 
     * 
     * 
     */
     
     public function getNodes($nodePid, $limit = false)
	{
		$parentNodeData = K_Tree::getNode($nodePid);
		
		$result = array();
		$data   = array();
		$rData  = array();
		
		$query = new K_Db_Query();
		$result = $query->q('SELECT `tr`.*, `ty`.* FROM `tree` AS `tr`, `type_'.$this->type.'` AS `ty` WHERE `tr`.`tree_id`=`ty`.`type_'.$this->type.'_id` AND `tr`.`tree_lkey`>'.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<'.$parentNodeData['tree_rkey'].' ORDER BY `tr`.`tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : ''));
		
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$this->type.'_', '', $typeField)] = $typeValue;
			}
		}
		
		return $rData;
	}
    
    
	/**
     * Возвращяет количетво дочерних нод определённого типа. 
     * 
     * 
     */
    
    
	public function count($nodePid)
	{
		$parentNodeData = K_Tree::getNode($nodePid);
	
		$query = new K_Db_Query();
		$result = $query->q('SELECT COUNT(*) FROM `tree` AS `tr` WHERE `tr`.`tree_lkey`>'.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<'.$parentNodeData['tree_rkey'].' AND `tr`.`tree_type`="'.$this->type.'"');
	
		$count = $result[0]->toArray();
		
		return $count['COUNT(*)'];
	}
    
    
	public static function delNodes($nodeid)
	{
    		$query = new K_Db_Query();
            $parentNodeData = K_Tree::getNode($nodeid); 
       
            $q='SELECT * FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].'  ORDER BY `tree_lkey` ASC';
                
              	$result = $query->q($q);
		               
                $usedTypes = array();
        		for ($i = 0; $i < sizeof($result); $i++)
        		{
        			$data[$i] = $result[$i]->toArray();
        			
        			if (!in_array($data[$i]['tree_type'], $usedTypes))
        			{
        				$usedTypes[] = $data[$i]['tree_type'];
        			}
        		}       
                     
              foreach ($usedTypes as $type)
        		{
                  $query->q('DELETE FROM type_'.$type.' where type_'.$type.'_id IN (SELECT tree_id FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].' )');
                 
         	    }     
     }
	
    
    /** Выбирает ветку ноды со всей её инфой ,которая храниться в смежных таблицах 
     * 
     * 
     */
	public static function gNodes($nodePid, $limit = false, $onlyChilds=false,$conditions='',$idIndex=false,$joins=false,$opt=false)
	{
		$rtypes = array();
		$rtypesTables = array();
		$rtypesAll = array();
		
		$query = new K_Db_Query();
        $parentNodeData = K_Tree::getNode($nodePid); 
         
        if($onlyChilds){
                if (is_numeric($nodePid)){
                   $pid=$nodePid;
                }
                else{
                  $node=K_Tree::getNode($nodePid);
                  $pid=$node['tree_id'];
                }
                    
                $q='SELECT * FROM tree where tree_pid="'.$pid.'" ORDER BY tree_lkey ASC limit 1';
          
           }
        else{
             $q='SELECT * FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].' '.($opt['filter'] ? ' AND tree_type="'.$opt['filter'].'"' : '').'  ORDER BY `tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : '');
     
          if ($opt['filter'])
             echo   $q;
            }
        
		$result = $query->q($q);
		
    
		$usedTypes = array();
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			if (!in_array($data[$i]['tree_type'], $usedTypes))
			{
				$usedTypes[] = $data[$i]['tree_type'];
			}
		}
		   
		$allResults = array();
		$rData = array();
     	// K_debug::get()->dump($usedTypes);
		$i = 1;
        $casCadeIds=array();
        // при каскаде убираем тип папка, он только будет мешать
        if($opt['cascade'] && $usedTypes[0]='folder'){            
           array_shift($usedTypes);           
        }        
        $casCadeStart=false;
		foreach ($usedTypes as $type)
		{
     	   //каскадная выборка(сперва выбираються ноды верхнего порядка по типу, к ним применяються условия, потом по pid выбираються ноды следующиего уровня)
                     $inStr='';
                        if ($opt['cascade'] && count($casCadeIds)){
                          $casCadeIdsIn=implode(',',$casCadeIds);
                          $inStr=' AND tr.tree_pid IN ('.$casCadeIdsIn.')';
                        }
                    
          
  			if (in_array($type, $usedTypes))
			{
				$rtypes[]       = $type;
				$rtypesTables = '`type_'.$type.'` AS ty ';
				$rtypesAll    = 'ty.*';
		    
                $tp=$type;
                
                //дополнительные условия для выборки 
                $rtypesWhere  = '`tr`.`tree_id`=ty.type_'.$type.'_id'. ($conditions[$type] ? $conditions[$type] :'');
                
		//	if ($conditions)echo 'SELECT `tr`.*, '.$rtypesAll.' FROM `tree` AS `tr`, '.$rtypesTables.' WHERE '.$rtypesWhere.' AND `tr`.`tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<='.$parentNodeData['tree_rkey'].' ORDER BY `tr`.`tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : '');
              
             // array('tour'=>array('resort'=>'to_city','hotel'=>'hotel','country'=>'country'));
         array('price'=>array('pid'=>array('max'=>'price')));
              
              $agreg='';
              $group='';
              
                              
              // группировки по типу, найти минимальное, максимальное, сумму для определённого типа
             if($opt['group'][$tp]){
                   $grp=$opt['group'][$tp];
               
                    foreach($grp as $k=>$v){
                        
                      $kCell='type_'.$tp.'_'.$k;  
                        
                      $group[]=" group by $k";
                      foreach($v as $t=>$y){
                      $agreg[]="$t(".'ty.type_'.$tp.'_'."$y)";
                      }
                                      
                  $agreg=','.implode(', ',$agreg);     
                  $group=implode(' ',$group);                 
                  }
             }
             
              //дополнительные джойны, для каждого типа, в дальнейшем зделать их автоматическими
              $jJoin='';
              $jTables='';
              if ($joins[$tp]){
                foreach($joins[$tp] as $k=>$v){
                  
                    $kTable='type_'.$k;   
                    $jTablesA[]="$kTable.*"; 
                    $jJoin[]=" LEFT JOIN $kTable ON $kTable.$kTable"."_id=ty.type_$tp".'_'."$v " ;   
                }
                
              $jJoin=implode(' ',$jJoin);     
              $jTables=','.implode(',',$jTablesA);                 
              } 
               
               //основной запрос
              $qv='SELECT `tr`.*, '.$rtypesAll.' '.$jTables.' '.$agreg.' FROM `tree` AS `tr`, '.$rtypesTables.' '.$jJoin.'  WHERE '.$rtypesWhere.' AND `tr`.`tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<='.$parentNodeData['tree_rkey'].'  '.$inStr.'  '.$group.' ORDER BY  `tr`.`tree_lkey` ASC  '.($limit ? ' LIMIT '.$limit : '');
	
              $result = $query->q($qv);
                
               if($opt['test']){
               K_debug::get()->addMessage($qv);
               }
                
                $casCadeIds=array();
                
 				foreach ($result as $key => $value)
				{
					$result[$key] = $value->toArray();
					$aResults=array();
					foreach ($result[$key] as $typeField => $typeValue)
					{
						$aResults[str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
                        
 			     	//	K_debug::get()->dump($key);
                	}
                   	$allResults[$aResults['tree_lkey']]=$aResults; 
                    
                    //каскадная выборка(сперва выбираються ноды верхнего порядка по типу, к ним применяються условия, потом по pid выбираються ноды следующиего уровня)
                    // сохраним id нод
                         if ($opt['cascade']){
                          $casCadeIds[]=$aResults["tree_id"];
                           
                        }
                     
                      
                  $i++; 
				}

              if ($opt['cascade']&&!count($casCadeIds)){break;};  
			//$allResults = array_merge($allResults, $rData);
			}
		}
		
        
        //K_debug::get()->dump($rAllResults);  
         
       //сортируем оп левому ключу:
        ksort($allResults);
        
        $claer=true;
        
        if($opt['group']){
                // если есть группировка  
               // нумеруем индексы по id
               $rrAllResults=array();
                foreach ($allResults as $key => $value)
    	    	{
    		    	$rrAllResults[$value['tree_id']] = $value;
    	    	}
    	        $allResults=$rrAllResults;
                
              //  K_debug::get()->dump($allResults); 
               
                 
               $rAllResults=array();
            foreach ($allResults as $key=>$value)
        		{
                    //если была группировка по этому типу то в массиве ответа к родительским нодам прикрепляем детей в виде индекса node_childs
                        $tp=$value['tree_type'];
                        if($opt['group'][$tp]){
                 
                            $tId=$value['tree_pid'];
                            
                          //  K_debug::get()->dump($tId);
                            $allResults[$tId]['node_childs'][]=$value['tree_id'];
                            
                            
                          // K_debug::get()->dump($rAllResults[$tId]['node_childs']);
                           }
                  
           		  }
               $claer=false;
        }    
         
         
        //если есть необходимость переназвать иднексы называем их по одному из полей ноды
       if ($idIndex){  
           $allResults;
    		foreach ($allResults as $value)
    		{
    		  if ($idIndex){
    		       $tId=$value['tree_id'];
    		      if(is_string($idIndex)) $tId=$value[$idIndex];   
                                   
          	     $rAllResults[$tId] = $value;  
       		  }
           }
           $claer=false;
        }
        
          // если всё чисто то просто прономеруем все элементы выборки по порядку   
        if($claer){
           $i=0;
            
            foreach ($allResults as $value)
	    	{
		    	$rAllResults[$i] = $value;
                $i++;
	    	}
          } 
          
          	if($opt['test']){ 
				K_debug::get()->dump($rAllResults);
            }
        
 		return $rAllResults;
	}
     
    
    
    public static function getTypeBlocks($nodePid, $limit = false)
	{
		$parentNodeData = K_Tree::getNode($nodePid);
		$typesModel = new K_Tree_Types_Model();
		
		$types = $typesModel->select('type_name')->fetchArray();
		$rtypes = array();
		$rtypesTables = array();
		$rtypesAll = array();
		
		$query = new K_Db_Query();
        
		$result = $query->q('SELECT * FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].' ORDER BY `tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : ''));
		
		$usedTypes = array();
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			if (!in_array($data[$i]['tree_type'], $usedTypes))
			{
				$usedTypes[] = $data[$i]['tree_type'];
			}
		}
		
		$allResults = array();
		$rData = array();
		
		$i = 1;
		foreach ($types as $type)
		{
			if (in_array($type['type_name'], $usedTypes))
			{
				$rtypes[]       = $type['type_name'];
				$rtypesTables = '`type_'.$type['type_name'].'` AS `ty'.$i.'`';
				$rtypesAll    = '`ty'.$i.'`.*';
				$rtypesWhere  = '`tr`.`tree_id`=`ty'.$i.'`.`type_'.$type['type_name'].'_id`';
				
				$result = $query->q('SELECT `tr`.*, '.$rtypesAll.' FROM `tree` AS `tr`, '.$rtypesTables.' WHERE '.$rtypesWhere.' AND `tr`.`tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<='.$parentNodeData['tree_rkey'].' ORDER BY `tr`.`tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : ''));
				
				foreach ($result as $key => $value)
				{
					$result[$key] = $value->toArray();
					
					foreach ($result[$key] as $typeField => $typeValue)
					{
						$rData[$key][str_replace('type_'.$type['type_name'].'_', '', $typeField)] = $typeValue;
					}
				}
				
				$allResults = array_merge($allResults, $rData);
				
				$i++;
			}
		}
		
		$rAllResults = array();
		
		foreach ($allResults as $key => $value)
		{
			$rAllResults[$value['tree_lkey']] = $value;
		}
		ksort($rAllResults);
		
		$rrAllResults = array();
		$i = 0;
		foreach ($rAllResults as $value)
		{
			$rrAllResults[$i] = $value;
		
			$i++;
		}
		
		return $rrAllResults;
	}

    // проверяет существует ли тип.
    
	public static function typeExists($type)
	{
		$typesModel = new K_Tree_Types_Model();
		
		$suchTypeCount = $typesModel->select()->where('`type_name`="'.$type.'"')->count();
		
		return $suchTypeCount;
	}
    
    // проверяет существует ли тип.
    
	public static function getType($type,$conditions,$join)
	{
		$typesModel = new K_Tree_Types_Model();
        
        if ($conditions)
         foreach ($conditions as $k=>$v){
           $wA[]=" type_$type".'_'."$k='$v' "; 
         };
         $w=implode('and ',$wA);
		$query = new K_Db_Query();
        
              foreach($join as $k=>$v){
                   
                    $kTable='type_'.$k;   
                    $jTablesA[]="$kTable.*"; 
                    $jJoin[]=" LEFT JOIN $kTable ON $kTable.$kTable"."_id=type_$type".'_'."$v " ;   
                }  
                
              $jJoin=implode(' ',$jJoin);     
              $jTables=','.implode(',',$jTablesA);         
              
           // echo 'SELECT * '.$jTables.' FROM type_'.$type.' '.$jJoin.' where '.$w;
            
	          $result=$query->q('SELECT * '.$jTables.' FROM type_'.$type.' '.$jJoin.' where '.$w);
       
				foreach ($result as $key => $value)
				{
					$result[$key] = $value->toArray();
					foreach ($result[$key] as $typeField => $typeValue)
					{
						$rData[$key][str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
					}
				}        
		return $rData;
	}
	
}