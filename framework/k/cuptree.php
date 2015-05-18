<?php 

/**
 * Class K_CupTree - дополнительные функции, надстройка над K_tree
 * 
 * Удаление, копирование всёх сущиностей типа и Ноды и Итема
 * <example>
 
 * </example>
 */

class K_CupTree {
    
    /** 
     *  Удаляет ноду и всех её детей вместе c итемами
     * 
     */
     
	public static function dNodes($nodeid)
	{
    		$query = new K_Db_Query();
            $parentNodeData = K_Tree::getNode($nodeid); 
       
            $q='SELECT * FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].'  ORDER BY `tree_lkey` ASC';
            $result = $query->q($q);
            $usedTypes = array();
            
        	for ($i = 0; $i < sizeof($result); $i++)
        		{
        			$data[$i] = $result[$i]->toArray();
                    // вызов статического метода утановленного на удаление
                    $typeController = 'Type_Controller_'.$data[$i]['tree_type']; 
                    if(method_exists($typeController,'onDelete')){
                       $typeController::onDelete($data[$i]);
                    }
        			
        			if (!in_array($data[$i]['tree_type'], $usedTypes))
        			{
        				$usedTypes[] = $data[$i]['tree_type'];
        			}
        		}       
                
            foreach ($usedTypes as $type)
        		{
        		  
                  $query->q('DELETE FROM type_'.$type.' where type_'.$type.'_id IN (SELECT tree_id FROM `tree` WHERE `tree_lkey`>='.$parentNodeData['tree_lkey'].' AND `tree_rkey`<='.$parentNodeData['tree_rkey'].' )');
           	    }  
                
          K_Tree::delete($nodeid);  
             
          return true;      
     }
     
    /** @function dTypeNodes удаляет все ноды определённого типа
     *  @param $type - название типа ноды которую надо удалить
     */ 
    
     public static function dTypeNodes($type)
     {
        
        /// Удаление всех нод определённого типа с вложенными детьми этих нод
        
        $query = new K_Db_Query;
        
        $typeNodes = $query->q('SELECT tree_id FROM tree WHERE tree_type='.K_DB_Quote::quote($type), true);
        
        foreach($typeNodes as $v){
            
               K_Tree::delete($v['tree_id']);
            
        }
             
     }
     
    /** 
     * Рекурсивно копирует детей одной ноды в другую без проверок на имена и прочую лабуду 
     * @param $copyNodeKey - ключь ноды из кторой копировать
     * @param $pasteNodeKey - ключь ноды в кторую копировать
     */
      
    private static function _сNodeRec($copyNodeKey,$pasteNodeKey)
	{ 
   	           $nodesArray = K_Tree::getChilds($copyNodeKey);
               if($nodesArray && count($nodesArray))
               foreach($nodesArray as $v){
                
                        $nodeId=K_Tree::add($pasteNodeKey, $v["tree_type"],$v["tree_name"],$v["tree_title"]);
                        eval('$typeModel=new Type_Model_'.ucfirst($v["tree_type"]).";");
             
                     	// Выполняем запрос с выбором итема копируюемой ноды
            			$result = $typeModel->select()->where("type_".$v["tree_type"].'_id='.$v["tree_id"])->fetchRow();
            	   	
            			if ($result)
            			{
            				$nodeItem = $result->toArray();
                             // дозаписываем информацию в таблицу типов
                             $nodeItem["type_".$v["tree_type"].'_id']=$nodeId;
                                
                             $typeModel->save($nodeItem); 
                        }
                        
                   //вызываем для каждой дочерней ноды      
                 self::_сNodeRec($v["tree_id"],$nodeId);      
               }
     }
     
     /** 
     *  Рекурсивно копирует ноду и всех её детей вместе с итемами в новое место 
     *  проверяет на копии и изменяет имя ноды если они есть, использует _сNodeRec для рекурсивного копирования детей
     * 
     */
	public static function сNode($copyNodeId,$pasteNodeId)
	{ 
               $nodeArray = K_Tree::getNode($copyNodeId);
               $nameLt = $nodeArray["tree_name"];
               $title = $nodeArray["tree_title"];
               
              if($pasteNodeId==$nodeArray["tree_pid"]){
                $nameLt.=base_convert(time()+rand(1,999),10,36);
                $title.="_K";
              }else {
               	$treeTable = new K_Tree_Model();
                //проверяем есть ли дети в ноде с таким именем если есть то меняем имя
		        $result = $treeTable->select()->where('`tree_pid` = '.$pasteNodeId.' AND `tree_name`="'.$nameLt.'"')->fetchAssoc('tree_name');
                  if (count($result))
            		{ 
                     $nameLt.="_".base_convert(time()+rand(1,999),10,36);
                     $title.="_K";   
                    }
              }
              
             $nodeId = K_Tree::add($pasteNodeId, $nodeArray["tree_type"],$nameLt,$title);
           
             //$cTree=new K_cTree($nodeArray["tree_type"]);
           
             //$nodeItem=$cTree->getItem($key,false);
           
             eval('$typeModel=new Type_Model_'.ucfirst($nodeArray["tree_type"]).";");
             
             	// Выполняем запрос
              
    			$result = $typeModel->select()->where("type_".$nodeArray["tree_type"].'_id='.$copyNodeId)->fetchRow();
    	   	
    			if ($result)
    			{
    				$nodeItem = $result->toArray();
    			}
         
             // дозаписываем информацию в таблицу типов
             $nodeItem["type_".$nodeArray["tree_type"].'_id']=$nodeId;
                
             $typeModel->save($nodeItem); 
               
             //рекурсивно копируем все дочерние ноды копируемой ноды в вновь созданную ноду но уже без проверок
             self::_сNodeRec($copyNodeId,$nodeId);
             return array("id"=>$nodeId,'title'=>$title);// возвращяет информацию о ноде новый id и тийтл который может измениться если в ветки есть копии 
     }
     
      // Сохраняет ноду в дерево и записывает данные в элемент типа
      public static function addNode($typeModel, $type, $tree, $nodeTitle, $nodeName, $saveData, $errSql = false, $isNew = false){
    
         if(empty($nodeName) || empty($nodeTitle)){
            return false;    
         }
		 
		 $nodes = K_TreeQuery::searcheByName($tree, $nodeName, $type, 1);
		 
		 if($nodes){
		 
			if($isNew){
		 
				return array( 'new' =>false,
							  'id'=>$nodes['tree_id']
							);    
				
			}else{
			
				return $nodes['tree_id'];    
				
			}
			
		 }	 

        $nodeId = K_Tree::add($tree, $type, strtolower(preg_replace("/[^a-z0-9-]/i","", K_string::rus2lat(preg_replace('/\s+/','-',trim($nodeName))))),trim($nodeTitle),1,0);
                                     
                                   if($nodeId){
                           
                                         $saveData['type_'.$type.'_id'] = $nodeId; 
										 
                                         K_Db_Adapter::$defaultAdapter->lastQueryError = false;
                                         
										 $typeModel->save($saveData);
                                        
                                        // echo DM_Db_Adapter::$defaultAdapter->lastSqlQuery;           
                                        // DM_cli::nbr();         
                                        
                                         if(K_Db_Adapter::$defaultAdapter->lastQueryError){
                                            
                                            if($errSql){ 
                                                echo K_Db_Adapter::$defaultAdapter->lastSqlQuery;
                                            }

                                            K_Tree::delete($nodeId);

                                         }else{

                                           	if($isNew){
		 
													return array(   'new' =>true,
																	'id'=>$nodeId
																);    
													
												}else{
														return $nodeId;    
												}
			

                                         }
                                   }
         return false;     
     }
     
        // обрабатывает массив выбранных нод удаляя рутовый путь у нод, в будующем переделать на класс
      public static function rootPath($nodesArr, $root) {
                        
            // добавить защиту от дураков (автоматическую добавку слешей) или выкинуть ошибку
            
            if(count($nodesArr)>0){
            
                foreach($nodesArr as $k=>$node){
                     
                  $nodesArr[$k]['tree_link'] = str_replace($root, '', $node['tree_link']);  
                    
                }
            
            }
            return $nodesArr;
        
      }
     
}
?>
