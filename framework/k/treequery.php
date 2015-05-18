<?php 

/**
 * Class K_TreeQuery - выбирает необходимые обьекты нод из структуры дерева
 * <example>
    $this->view->toures=K_TreeQuery::crt("181")
                                               ->condit(array('tour' =>' and type_tour_country="'.$this->view->country['tree_id'].'"'))
                                               ->idIndex(true)
                                               ->joins(array('tour'=>array(
                                                                        'resort'=>'to_city',
                                                                        'hotel'=>'hotel',
                                                                        'country'=>'country'
                                                                       )
                                                      )
                                               )
                                               ->group(
                                                       array(
                                                                'price'=>array(
                                                                   'tr.tree_pid'=>array(
                                                                                    'min'=>'cost'
                                                                                    )
                                                                        )
                                                       )
                                               )
                                               ->go(array('test'=>true)); 
                                               
                                               $articles = K_TreeQuery::crt('/articles/')->types('articles')->order(array('articles'=>'date_publication'))->limit(5)->go(array('orderby'=>'ASC','test'=>true));   
                                                
                                               
                                               
 * </example>
 */

class K_TreeQuery {
     
     protected $limit;
     protected $condit;
     protected $idIndex;
     protected $joins;
     protected $group;
     protected $nid;
     protected $type;
     protected $parentNodeData = false; 
     static protected $instance = false; 
     
	public function __construct($nodePid = 0) {
        $this->parentNodeData = K_Tree::getNode($nodePid); 
        $this->nid = $nodePid;
 	}
    
    public function reset() {
      $this->limit = false;
      $this->condit = '';
      $this->idIndex = false;
      $this->joins = false;
      $this->group = false;
      $this->nid = false;
      $this->type = false;
      $this->types = false;
      return $this;    
  	}
	
	static public function crt( $nodePid = 0) {
	       if ($instance){
	           $instance->reset();
               $this->parentNodeData = K_Tree::getNode($nodePid); 
               $this->nid = $nodePid;
 	           return  $instance;    
  	       }   
           else {
               return  $instance = new K_TreeQuery( $nodePid );
           }
	}
    
	public function limit( $limit, $offset ) {
			$this->limit = $limit;
  	        $this->offset = $offset;
   		return $this;
	}

    public function limitLikeSql($offset, $limit = false ) {

        if($limit==false){

            $limit = $offset;

        }

        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }
    
    public function condit( $data ) {
		$this->condit = $data;
		return $this;
	}
    
    public function idIndex( $data ) {
		$this->idIndex = $data;
		return $this;
	}
    
    public function joins( $data ) {
		$this->joins = $data;
		return $this;
	}
    
    public function group( $data ) {
		$this->group = $data;
		return $this;
	}
    
    public function order( $data ) {
		$this->order = $data;
		return $this;
	}
    
    public function type( $data ) {
	    $this->types($data);
		return $this;
	}
    
    public function types($data) {
        
        if(is_string($data)){
			$this->types=array_map('trim',explode(',',$data));
        }
        else{
           $this->types = $data;
        }
        return $this;
	}
   
    public function go($more=array()) {
        
        $more = array_merge(array('cascade' => false, "childs" => false, "test" => false, 'count' => false, 'aliases' => false, "tree_name" => false), $more);
    
    	return K_TreeQuery::gNodes(
                                array(
                                   "id"=>!empty($this->nid)? $this->nid : 0 ,        
                                   "limit"=>!empty($this->limit)? $this->limit : false ,
                                   "offset"=>!empty($this->offset)? $this->offset : 0,
                                   "types"=>!empty($this->types)? $this->types : false,
                                   "condit"=>!empty($this->condit)? $this->condit : '' ,
                                   'filter'=>!empty($this->filter)? $this->filter : false ,
                                   "idIndex"=>!empty($this->idIndex)? $this->idIndex : false ,
                                   "joins"=>!empty($this->joins)? $this->joins : false ,
                                   "group"=>!empty($this->group)? $this->group : false ,
                                   "order"=>!empty($this->order)? $this->order : false ,
                                   "more"=>$more
                                ), false, $this->parentNodeData
                             );
	  
    }
    
    public function goLite(){
    	   return K_TreeQuery::gNodesLite($this->nid,$this->type,$this->limit);
    }
       
    public function one(){
     	   return K_TreeQuery::gOne($this->nid);
    }    
        
    public function getUseNode(){
      	   return $this->parentNodeData;
    }
        
   
   	public static function gNodesLite($nodePid, $type, $limit = false)
	{
		$parentNodeData = K_Tree::getNode($nodePid);
		
		$result = array();
		$data   = array();
		$rData  = array();
		
		$query = new K_Db_Query();
		$result = $query->q('SELECT `tr`.*, `ty`.* FROM `tree` AS `tr`, `type_'.$type.'` AS `ty` WHERE `tr`.`tree_id`=`ty`.`type_'.$type.'_id` AND `tr`.`tree_lkey`>'.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<'.$parentNodeData['tree_rkey'].' ORDER BY `tr`.`tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : ''));
		
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
			}
		}
		
		return $rData;
	}
	
	// todo сделать тип не обязательным  доделать searcheByName
	
	public static function searcheByName($sercheNode, $nodeName, $type=false, $deep=0)
	{
        		  
	    if (!$parentNodeData){
		
          $parentNodeData = K_Tree::getNode($sercheNode); 
		  
        }
         
  		$result = array();
		$data   = array();
		$rData  = array();
		
		$query = new K_Db_Query();
		
		//echo 'SELECT `tr`.*, `ty`.* FROM `tree` AS `tr` LEFT JOIN `type_'.$type.'` AS `ty` ON tr.tree_id  = ty.type_'.$type.'_id  WHERE tr.tree_lkey >= '.(int)$parentNodeData['tree_lkey'].' AND tr.tree_rkey <= '.(int)$parentNodeData['tree_rkey'] .' AND tr.tree_name = "'.$nodeName.'"';
		
		if($type){
		
			$queryType = ' AND tr.tree_type="'.$type.'"';
			
		}
		
		if($deep>0){
		
			$queryDeep = ' AND tr.tree_level<="'.($parentNodeData['tree_level']+$deep).'"';
			
		}
				
		$result = $query->q('SELECT `tr`.*, `ty`.* FROM `tree` AS `tr` LEFT JOIN `type_'.$type.'` AS `ty` ON tr.tree_id  = ty.type_'.$type.'_id  WHERE tr.tree_lkey >= '.(int)$parentNodeData['tree_lkey'].' AND tr.tree_rkey <= '.(int)$parentNodeData['tree_rkey'] .' AND tr.tree_name like "'.$nodeName.'"'.$queryType.$queryDeep);
		
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
			}
		}
		
		return $rData[0];
	}
    
 	public static function gOne($nodePid,$type=false)
	{
	
		if (!$type){       
			
			if (!$parentNodeData){
			  $parentNodeData = K_Tree::getNode($nodePid); 
			}
			
			$type = $parentNodeData['tree_type'];
			
		}
             
        $key_field = is_numeric($nodePid) ? 'tree_id' : 'tree_link';
        
  		$result = array();
		$data   = array();
		$rData  = array();
		
		$query = new K_Db_Query();
		$result = $query->q('SELECT `tr`.*, `ty`.* FROM `tree` AS `tr` LEFT JOIN `type_'.$type.'` AS `ty` ON tr.tree_id  = ty.type_'.$type.'_id  WHERE tr.'.$key_field.' = "'.$nodePid.'"');
				
        // если найденная нода не соответствует типу запрашиваемой ноды возвращяем false    
        if(	!$result || $result[0]['tree_type']!=$type){
            return false;
        }
        
        
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
			}
		}
		return $rData[0];
	}
  
  
   	public static function gBro($nodePid,$type=false)
	{
      if (!$type){       
	    if (!$parentNodeData){
          $parentNodeData = K_Tree::getNode($nodePid); 
        }
        $type=$parentNodeData['tree_type'];
	   }
        
  		$result = array();
		$data   = array();
		$rData  = array();
		
		$query = new K_Db_Query();
		$result = $query->q('SELECT `tr`.*, `ty`.* FROM `tree` AS `tr` LEFT JOIN `type_'.$type.'` AS `ty` ON tr.tree_id  = ty.type_'.$type.'_id  WHERE tr.tree_pid= "'.$nodePid.'"');
        
		for ($i = 0; $i < sizeof($result); $i++)
		{
			$data[$i] = $result[$i]->toArray();
			
			foreach ($data[$i] as $typeField => $typeValue)
			{
				$rData[$i][str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
			}
		}
		return $rData;
	}
    
    /** Выбирает ветку ноды со всей её инфой ,которая храниться в смежных таблицах типов
     * 
     * 
     */
     
	public static function gNodes($opt, $limit = false, $parentNodeData = false)
	{
	       
		// Этап 1 формирование настроек для выполнения  
     
		// для поддержки старого упрощенного варианта  
		
		if(!is_array($opt)){
		
			$opt = array("id"=>$opt,"limit" => $limit);
		
		}  
    
		$opt = array_merge(array(
	 
						 "id"      => 0,        
						 "limit"   => false,
						 "offset"  => 0,
						 "types"   => false,
						 "condit"  => '',
						 'filter'  => false,
						 "idIndex" => false,
						 "joins"   => false,
						 "group"   => false,
						 "order"   => false,
						 "more"    => array('cascade'=>false, "childs"=>false, "test"=>false, 'meta', 'count'=>false, 'orderby'=>"ASC", "tree_name" => false, "aliases" => false, 'leveldesc'=>false ) // умолчания настраиваются в go
                     
						), $opt);
    	
  		$rtypes = array();
		$rtypesTables = array();
		$rtypesAll = array();
		
		$query = new K_Db_Query();
        
        if (!$parentNodeData){
			$parentNodeData = K_Tree::getNode($opt["id"]); 
        }
       
        //Этап 2 выборка и формирование списка типов с которыми будем работать
       
       if(!$opt['types'])
       {   
            if($opt['more']['childs']){
			
			
                   /*
                    if (is_numeric($opt["id"])){
                       $pid=$opt["id"];
                    }
                    else{
                      $node=K_Tree::getNode($opt["id"]);
                      $pid=$node['tree_id'];
                    }*/
                     $q ='SELECT DISTINCT tree_type FROM tree where tree_pid="'.$parentNodeData['tree_id'].'" ORDER BY tree_lkey ASC limit 1';
                     $result = $query->q($q);
               }
            else{
                 $q ='SELECT DISTINCT tree_type FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].' '.($opt['filter'] ? ' AND tree_type="'.$opt['filter'].'"' : '').'  ORDER BY `tree_lkey` ASC '.($opt["limit"] ? ' LIMIT '.$opt["limit"] : '');
                 $result = $query->q($q);
            }
        
    		$usedTypes = array();
    		for ($i = 0; $i < sizeof($result); $i++)
    		{
    			$data[$i] = $result[$i]->toArray();
    			
    			if (!in_array($data[$i]['tree_type'], $usedTypes))
    			{
    				$usedTypes[] = $data[$i]['tree_type'];
    			}
    		}
    		   
    		
         	// K_debug::get()->dump($usedTypes);
            //этап 3 выборка итемов требуемых типов
            $allResults = array();
    		$rData = array();
            $i = 1;
            $topLevel=true;
             $casCadeStart=false;
            $casCadeIds=array();
            // при каскаде убираем тип папка, он только будет мишать
            if($opt['more']['cascade'] && $usedTypes[0]='folder'){            
               array_shift($usedTypes);           
            }  
             
        }
        else{
            $usedTypes=$opt['types'];
        }; 
         
		foreach ($usedTypes as $type)
		{
     	   //каскадная выборка(сперва выбираються ноды верхнего порядка по типу, к ним применяються условия, потом по pid выбираються ноды следующиего уровня)
                     $inStr='';
                        if ($opt['more']['cascade'] && count($casCadeIds)){
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
                $rtypesWhere  = '`tr`.`tree_id`=ty.type_'.$type.'_id'. ($opt['condit'][$type] ? $opt['condit'][$type] :'');
                
		//	if ($conditions)echo 'SELECT `tr`.*, '.$rtypesAll.' FROM `tree` AS `tr`, '.$rtypesTables.' WHERE '.$rtypesWhere.' AND `tr`.`tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<='.$parentNodeData['tree_rkey'].' ORDER BY `tr`.`tree_lkey` ASC '.($limit ? ' LIMIT '.$limit : '');
              
             // array('tour'=>array('resort'=>'to_city','hotel'=>'hotel','country'=>'country'));
         //array('price'=>array('pid'=>array('max'=>'price')));
              
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
             
              //дополнительные джойны, для каждого типа, в дальнейшем cделать их автоматическими
              $jJoin='';
              $jTables='';
              if ($opt['joins'][$tp]){
                foreach($opt['joins'][$tp] as $k=>$v){
                  
                    $kTable='type_'.$k;   
                    $jTablesA[]="$kTable.*"; 
                    $jJoin[]=" LEFT JOIN $kTable ON $kTable.$kTable"."_id=ty.type_$tp".'_'."$v " ;   
                }
                
              $jJoin=implode(' ',$jJoin);     
              $jTables=','.implode(',',$jTablesA);                 
              } 
              
              $limitStr='';
              
             if(!$opt['more']['count']){
               $topLevel=false; 
             } 
             
             // при каскадной выборке лимит работает только для верхнего типа
             if($opt['more']['cascade']){
                 if ($topLevel){
                   $limitStr=($opt['limit'] ? ' LIMIT  '.$opt['offset'].', '.$opt['limit'] : '');
                 }
               }
             else{	// при простой выборке лимит делиться на все типы по чучуть
           
               if(count($usedTypes)>1){
           
                   $typeCount = count($usedTypes);
                   $limitForType = floor($opt['limit']/$typeCount);
                   $offsetForType = floor($opt['offset']/$typeCount);
                      
                   if ($topLevel){// разницу добавим к верхнему уровню:К ПРИМЕРУ 10 разобьёться на троих так 4 3 3
                      $limitForType += $opt['limit']-$limitForType*$typeCount;
                      $offsetForType += $opt['offset']-$offsetForType*$typeCount;
                   }
                   $limitStr = ($limitForType ? ' LIMIT  '.$offsetForType.', '.$limitForType : '');
                
                }else{
                    
                    if($opt['limit']){
                            $limitStr = (' LIMIT  '.$opt['offset'].', '.$opt['limit']);  
                    }
                    
                }
                
             } 
             
                 
          // сортировка, если её нет то сортируеться по левому ключу
             if($opt['order'][$tp]){
                    
                if(is_string($opt['order'][$tp])){
                   $opt['order'][$tp]=array($opt['order'][$tp]);
                }
                   $orderArr = array();  
                 
                if(is_string($opt['more']['orderby'])){
                   $opt['more']['orderby'] = array($opt['more']['orderby']);
                }  
                
                $ior = 0; 
                
                foreach($opt['order'][$tp] as $v){
                       $orderArr[]='ty.'.'type_'.$tp.'_'.$v.' '.$opt['more']['orderby'][$ior];
                       $ior ++;
                      }
                      
                 $orderFields = implode(',',$orderArr);
                 
                 $orderBY =' ORDER BY  '.$orderFields.' ';

             }elseif($opt['more']['leveldesc']){
			 
			  $orderBY =' ORDER BY  `tr`.`tree_level` ASC ';
			 
			 }
			 else{
                
                $orderBY =' ORDER BY  `tr`.`tree_lkey` ASC  ';
             }
			 		
              //основной запроc,
            $qv = 'SELECT'.($limitStr?' SQL_CALC_FOUND_ROWS ' : '').' `tr`.*, '.$rtypesAll.' '.$jTables.' '.$agreg.' FROM `tree` AS `tr`, '.$rtypesTables.' '.$jJoin.'  WHERE '.$rtypesWhere.' '.($opt['more']['tree_name']===false ?  ' ':' AND tree_name="'.$opt['more']['tree_name'].'"' ).($opt['more']['childs']? ' AND `tr`.`tree_level`='.($parentNodeData['tree_level']+1):'').' AND `tr`.`tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tr`.`tree_rkey`<='.$parentNodeData['tree_rkey'].'  '.$inStr.'  '.$group.$orderBY.$limitStr;
          
		    $result = $query->q($qv);
              
            if ($limitStr){
               $сountResult = $query->q("SELECT FOUND_ROWS() as cItems;");
               $countItems+=$сountResult[0]['cItems'];
            }
               
               if($opt['more']['test']){
                 K_debug::get()->addMessage($countItems);
               }
               
               if($opt['more']['test']){
                 K_debug::get()->addMessage($qv);
               }
                
                $casCadeIds = array();
                
 				foreach ($result as $key => $value)
				{
			        
					$result[$key] = $value->toArray();
					
                    $aResults = array();
				
                	foreach ($result[$key] as $typeField => $typeValue)
					{
						$aResults[str_replace('type_'.$type.'_', '', $typeField)] = $typeValue;
                        
 			     	//	K_debug::get()->dump($key);
                	}            
                                                       
                    if( $opt['more']['aliases'] == true && $aResults['tree_type'] == 'alias'){
                     
                        $aliases[$aResults['tree_lkey']] = $aResults['elementid'];
                      //  var_dump( $aResults);
                        
                    }
                    
                   	$allResults[$aResults['tree_lkey']] = $aResults; 
                    //каскадная выборка(сперва выбираються ноды верхнего порядка по типу, к ним применяються условия, потом по pid выбираються ноды следующиего уровня)
                    // сохраним id нод
                    
                         if ($opt['more']['cascade']){
                             $casCadeIds[] = $aResults["tree_id"];
                         }
                         
                  $i++; 
				}
         
              if ($opt['more']['cascade']&&!count($casCadeIds)){break;};  
			//$allResults = array_merge($allResults, $rData);
              $topLevel = false;
			}
		}
		
        //Этап 4 постобработка, получение данных в нужном нам виде ,подключение алиасов
        /**
         *@todo группировка итемов по типу и вывод в массив   
         * 
         * 
         **/
        //K_debug::get()->dump($rAllResults);  
       
        //проверка на алиасы, если они есть выбираем все элементы по алиасам и заменяем ими алиасы
   //    var_dump($aliases);
        $aliasesIdsStr = implode(',', array_unique($aliases));
                
        if(count($aliases) && $aliasesIdsStr){
            
           //Узнаём все типы алиасов 
                  
            $q = 'SELECT DISTINCT tree_type FROM tree where tree_id IN('.implode(',',$aliases).')';
            
            $aliasesTypesResult = $query->q($q);
            
            //массив таблиц из которых будем выбирать
            foreach($aliasesTypesResult as $v){
                
                $aliasesTypes[] = $v['tree_type'];
             
                $aliasesTypesTables[] ='type_'.$v['tree_type'];
               
                $aliasesTypesTablesWhere[] = 'type_'.$v['tree_type'].'.'.'type_'.$v['tree_type'].'_id = tree.tree_id';
            }
            
            //массив условий для выбора типов
            $qv = 'SELECT * FROM tree, '.implode(',', $aliasesTypesTables).' WHERE tree.tree_id IN('.implode(',',$aliases).') AND '.implode(',', $aliasesTypesTablesWhere);
          
            $aliasesResult = $query->q($qv);
             
            foreach($aliasesResult as $v){
             
                $aliasesResultKeyId[$v['tree_id']] = K_Cupitems::stripFieldsArr($v ,'type_'.$v['tree_type']);
             
            }
            
            //var_dump($aliasesResultKeyId);
            foreach($allResults as $v){
                    
                if($v['tree_type'] == 'alias'){ 
              
                    $elementid = $aliases[$v['tree_lkey']];
           
                    unset($allResults[$v['tree_lkey']]['tree_type']);
                 
                    $aliasElementResult = array_merge($aliasesResultKeyId[$elementid], $allResults[$v['tree_lkey']]);                    
                   
                    $allResults[$v['tree_lkey']] = $aliasElementResult;
                      
                    //$aliasesResultKeyId[$elementid];
                       
                    // array_merge($aliasesResultKeyId[$elementid],$allResults[$v['tree_lkey']]);
                  
                    // var_dump($aliasesResultKeyId[$elementid]);
                }
            }
        }
               
        //сортируем по левому ключу:
       
        if(!$opt['order'])
        ksort($allResults);
        
        $claer = true;
        
        if($opt['group']){
            
               // если есть группировка  
               // нумеруем индексы по id
               $rrAllResults = array();
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
               $claer = false;
        }    
         
         
        //если есть необходимость переназвать иднексы называем их по одному из полей ноды
       if ($opt['idIndex']){  
           $allResults;
    		foreach ($allResults as $value)
    		{
    		  if ($opt['idIndex']){
    		       $tId=$value['tree_id'];
    		      if(is_string($idIndex)) $tId=$value[$opt['idIndex']];   
                                   
          	     $rAllResults[$tId] = $value;  
       		  }
           }
           $claer=false;
        }
        
          // если всё чисто то просто пронумеруем все элементы выборки по порядку   
        if($claer){
           $i=0;
            
            foreach ($allResults as $value)
	    	{
		    	$rAllResults[$i] = $value;
                $i++;
	    	}
          } 
          
          	if($opt['more']['test']){ 
			var_dump($qv);
				K_debug::get()->dump($rAllResults);
            }
          
            if($opt['more']['meta']){
               return array($rAllResults, $parentNodeData);
            }  
            
            if($opt['more']['count']){
               return array($rAllResults, $countItems);
            }  
            
 		return $rAllResults;
	}
}
?>
