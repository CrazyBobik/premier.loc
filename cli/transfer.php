<?php

ini_set ( 'max_execution_time', 1*60*60);
ini_set ( 'memory_limit ', '124M' );

defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));


require_once realpath(dirname(__FILE__) . '/../configs/loader.php');

 	$content = K_q::query('SELECT * from type_product');
		
    foreach($content as $v){
	
		if(!empty($v['type_product_img'])){
		
			$pt = pathinfo ($v['type_product_img']);
		
			$clean = k_string::urlClear(k_string::forKey($pt['filename']));
		
			if(empty($clean)){
			
				$clean = uniqid();
				
			}
		
			//.'/'.$clean.'.'.$pt['extension'].'
			//echo $v['type_product_img'].'<br/>';//	$save = $pt['dirname'].'<br/>';
			
			$save = $pt['dirname'].'/'.$clean.'.'.$pt['extension'] ;
			
			K_q::query("UPDATE type_product set type_product_img='".$save."' WHERE type_product_id=".$v['type_product_id']);
			
			//echo WWW_PATH.'/upload/'.$v['type_product_img']. "=>". WWW_PATH.'/upload/'. $pt['dirname'] .'/'.$clean.'.'.$pt['extension'] .'<br/>';
			
			echo copy(WWW_PATH.'/upload/'.$v['type_product_img'], WWW_PATH.'/upload/'.$pt['dirname'].'/'.$clean.'.'.$pt['extension']);
			 
		}
		 
	}
    
   /*
   $content = K_q::query('SELECT * from type_productcategory');
   foreach($content as $v){
		
	//	K_q::query('UPDATE set type_product_content="'.str_replace('/upload/фото для наполнения/фото для наполнения','/upload/content/images/foto',$v['type_product_content']).'",
		//							 type_product_img="'.str_replace('/upload/фото для наполнения/фото для наполнения','/upload/content/images/foto',$v['type_product_img']).'" WHERE type_product_id='.$v['type_product_id']);
		

		//K_q::query('UPDATE type_product set type_product_img="'.str_replace('фото для наполнения/фото для наполнения','content/images/foto',$v['type_product_img']).'" WHERE type_product_id='.$v['type_product_id']);
// K_q::query('UPDATE type_product set type_product_img="'.str_replace('фото для наполнения/','content/images/foto/',$v['type_product_img']).'" WHERE type_product_id='.$v['type_product_id']);
// K_q::query("UPDATE type_product set type_product_content='".addslashes(str_replace('/upload/content/foto','/upload/content/images/foto',$v['type_product_content']))."' WHERE type_product_id=".$v['type_product_id']);
 //echo "UPDATE type_product set type_product_content='".str_replace('фото%20для%20наполнения/фото%20для%20наполнения','/upload/assets/',$v['type_product_content'])."' WHERE type_product_id=".$v['type_product_id'];
 ///фото для наполнения/
		K_q::query("UPDATE type_productcategory set type_productcategory_content='".addslashes(str_replace('фото%20для%20наполнения/фото%20для%20наполнения','/upload/content/images/foto',$v['type_productcategory_content']))."' WHERE type_productcategory_id=".$v['type_productcategory_id']);
 
// echo str_replace('фото для наполнения/фото для наполнения','content/images/foto',$v['type_product_img']);
 //var_dump('UPDATE set type_product_content="'.str_replace('/upload/фото для наполнения/фото для наполнения','/upload/content/images/foto',$v['type_product_content']).'",
				//					 type_product_img="'.str_replace('/upload/фото для наполнения/фото для наполнения','/upload/content/images/foto',$v['type_product_img']).'" WHERE type_product_id='.$v['type_product_id']);
   }

/*
  echo '1';
	$content = K_q::query('SELECT * from weblumie_energomir_site_content');	 
	
	foreach($content as $v){
			 
			 
			 
		//echo ('UPDATE * weblumie_energomir_site_content set level='.substr_count(trim($v['uri'],'/'),'/').' WHERE id='.$v['id']);	 
		K_q::query('UPDATE weblumie_energomir_site_content set level='.substr_count(trim(trim($v['uri']),'/'),'/').' WHERE id='.$v['id']);	 
					
	}
	
	
	$content = K_q::query('SELECT * from weblumie_energomir_site_content c LEFT JOIN weblumie_energomir_site_tmplvar_contentvalues v ON c.id=v.contentid and v.tmplvarid=19 WHERE c.uri like "%sparkwave-–-radiorelejnyie-sistemyi-peredachi%" and c.isfolder=0 order by level');	 

	
		//K_q::query('UPDATE `type_product` SET type_product_img="'.$v['value'].'" WHERE type_product_name like "'.$v['pagetitle'].'"');
	
	
	//}
	  var_dump($content);      
foreach($content as $v){ 

		
			$typeM = new Type_Model_Product; 
			$type='product'; 
			
			$data = array(
			
				'type_product_name' => $v['pagetitle'],  
				'type_product_content' => $v['content'],
				'type_product_longtitle' => $v['longtitle'],
				'type_product_img' => $v['value']
					   
			);
	
		            
				
			
				
        K_CupTree::addNode($typeM, $type, '/products/ru/iskra-sloveniya/sparkwave--radiorelejnyie-sistemyi-peredachi/', $v['pagetitle'], trim($v['alias'], '/'), $data, $errSql = false);
		
	}
 */	
/*
    foreach($content as $v){ 

		if($v['isfolder']=='1'){
		
			$typeM = new Type_Model_Productcategory; 
			
			$type='productcategory';	
			
			$data = array(
                
				'type_productcategory_name' => $v['pagetitle'],  
				'type_productcategory_content'=> $v['content'],
				'type_productcategory_longtitle'=> $v['longtitle']
                           
            );
       
		}else{
		
			$typeM = new Type_Model_Product; 
			$type='product'; 
			
			$data = array(
			
				'type_product_name' => $v['pagetitle'],  
				'type_product_content' => $v['content'],
				'type_product_longtitle' => $v['longtitle'],
				'type_product_img' => $v['value']
					   
			);
	
		}                         
				
		$linkArray = explode('/', trim(trim($v['uri']), '/'));
		
		if(count($linkArray)>1){
			
			$linkArray = array_slice($linkArray, 0, count($linkArray)-1);
						
			$link = implode('/', $linkArray);
		           
		}else{
		
			$link = false;
			
		}
			
        K_CupTree::addNode($typeM, $type, '/products/ru/'.($link? $link.'/':''), $v['pagetitle'], trim($v['alias'], '/'), $data, $errSql = false);
		
	}
 
//include('classes/db.php');

/*

include(ROOT_PATH.'/configs/all_config.php');

include K_PATH.'/loader.php';

spl_autoload_register(array('K_Loader', 'auto_load'));
*/
/*
K_Query::connSettings(array(
                                     'host'=>AllConfig::$mysqBDConf['host'],
								     'login'=>AllConfig::$mysqBDConf['user'],
								     'password'=>AllConfig::$mysqBDConf['password'],
							    	 'dbname'=>AllConfig::$mysqBDConf['database'],
								     'port' => 3306
               		  ));
                      */
	/*				  
    $clients = K_Query::query('SELECT * FROM `wp_posts` WHERE post_type="post" AND post_status="publish"');			  
   
    $typeM = new Type_Model_News; 
    
    foreach($clients as $v){ 
          
            $content = explode('<!--:-->', $v['post_content']);
            $title = explode('<!--:-->', $v['post_title']);
            
            $data = array(
            
                'type_news_head' => str_replace('<!--:ru-->','', $title[0]),  
                'type_news_head_uk'=> str_replace('<!--:uk-->','', $title[1]),
                
                'type_news_content'=> str_replace('<!--:ru-->','', $content[0]),
                'type_news_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                'type_news_date' => $v['post_date']
       
                  );
       
            K_CupTree::addNode($typeM, 'news', '/novosti/', str_replace('<!--:ru-->','', $title[0]), $v['post_name'], $data, $errSql = false);	
 
	}	*/
    
   	/*		  
    $clients = K_Query::query('SELECT * FROM `wp_posts` WHERE `post_type` LIKE "%page%" and post_parent="0"');			  
   
    $typeM = new Type_Model_Articles; 
    
    foreach($clients as $v){ 
          
            $content = explode('<!--:-->', $v['post_content']);
            $title = explode('<!--:-->', $v['post_title']);
            
            $data = array(
                
                    'type_articles_content' => str_replace('<!--:ru-->','',$content[0] ),  
                    'type_articles_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                    
                    'type_articles_header'=> str_replace('<!--:ru-->','',$title[0] ),
                    'type_articles_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
         
                  );
       
            K_CupTree::addNode($typeM, 'articles', '/articles/', str_replace('<!--:ru-->','', $title[0]), $v['post_name'], $data, $errSql = false);	
 	}
    
    
   */
   
   // перенос сео 
   /*
    $clients = K_Query::query('SELECT * FROM tree t LEFT JOIN wp_posts p ON  t.tree_name=p.post_name LEFT JOIN wp_postmeta pm ON pm.post_id=p.ID WHERE pm.meta_key="title"
  ');			  
    
    foreach($clients as $v){ 
          
            $tok = explode('<!--:-->', $v['meta_value']);
            K_Query::query('UPDATE tree SET tree_meta_title="'.str_replace('<!--:ru-->','',$tok[0] ).'", tree_meta_title_uk="'.str_replace('<!--:uk-->','',$tok[1]).'" WHERE tree_id="'.$v['tree_id'].'"');			
        
   	}
        
    $clients = K_Query::query('SELECT * FROM tree t LEFT JOIN wp_posts p ON  t.tree_name=p.post_name LEFT JOIN wp_postmeta pm ON pm.post_id=p.ID WHERE pm.meta_key="description"
  ');			  
     
    foreach($clients as $v){ 
          
            $tok = explode('<!--:-->', $v['meta_value']);
            K_Query::query('UPDATE tree SET tree_meta_description="'.str_replace('<!--:ru-->','',$tok[0] ).'", tree_meta_description_uk="'.str_replace('<!--:uk-->','',$tok[1]).'" WHERE tree_id="'.$v['tree_id'].'"');				
            
   	}

    $clients = K_Query::query('SELECT * FROM tree t LEFT JOIN wp_posts p ON  t.tree_name=p.post_name LEFT JOIN wp_postmeta pm ON pm.post_id=p.ID WHERE pm.meta_key="keywords"
  ');			  
     
    foreach($clients as $v){ 
          
            $tok = explode('<!--:-->', $v['meta_value']);
            K_Query::query('UPDATE tree SET tree_meta_keywords="'.str_replace('<!--:ru-->','',$tok[0] ).'", tree_meta_keywords_uk="'.str_replace('<!--:uk-->','',$tok[1]).'" WHERE tree_id="'.$v['tree_id'].'"');		
            
   	}
     
    /*
    
    		  
    $clients = K_Query::query('SELECT * FROM `wp_posts` WHERE guid LIKE "http://www.glassok.ua/nashi-klienty/%"');			  
   
    $typeM = new Type_Model_Clients; 
    
    foreach($clients as $v){ 
          
            $content = explode('<!--:-->', $v['post_content']);
            $title = explode('<!--:-->', $v['post_title']);
            
            $data = array(
                
                    'type_clients_header' => str_replace('<!--:ru-->','', $title[0]),  
                    'type_clients_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                    
                    'type_clients_content'=> str_replace('<!--:ru-->','', $content[0]),
                    'type_clients_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                           
                  );
       
            K_CupTree::addNode($typeM, 'clients', '/clients/', str_replace('<!--:ru-->','', $title[0]), $v['post_name'], $data, $errSql = false);	
 
	};  
    */
    /*
              
                  $typesFields = array(
                
                       'clients' => array(
                            
                                'type_clients_header' => str_replace('<!--:ru-->','', $title[0]),  
                                'type_clients_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                                
                                'type_clients_content'=> str_replace('<!--:ru-->','', $content[0]),
                                'type_clients_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                                       
                              ),
                
                       'articles' => array(
                            
                                'type_articles_content' => str_replace('<!--:ru-->','',$content[0] ),  
                                'type_articles_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                                
                                'type_articles_header'=> str_replace('<!--:ru-->','',$title[0] ),
                                'type_articles_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                     
                              ),
                              
                        'news' => array(
                        
                            'type_news_head' => str_replace('<!--:ru-->','', $title[0]),  
                            'type_news_head_uk'=> str_replace('<!--:uk-->','', $title[1]),
                            
                            'type_news_content'=> str_replace('<!--:ru-->','', $content[0]),
                            'type_news_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                   
                              ),
                          
                        'produkciya' => array(
                        
                            'type_produkciya_header' => str_replace('<!--:ru-->','', $title[0]),  
                            'type_produkciya_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                            
                            'type_produkciya_content'=> str_replace('<!--:ru-->','', $content[0]),
                            'type_produkciya_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                     
                          )    
                
                );
    
    
    $posts = K_Query::query('SELECT * from tree t INNER JOIN wp_posts wp ON t.tree_name = wp.post_name');			  
    
    foreach($posts as $v){
        
            if(in_array($v['tree_type'], array_keys($typesFields))){
                     
                  $content = explode('<!--:-->', $v['post_content']);
                  $title = explode('<!--:-->', $v['post_title']);
                    
                    
                  $typesFields = array(
                
                       'clients' => array(
                            
                                'type_clients_header' => str_replace('<!--:ru-->','', $title[0]),  
                                'type_clients_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                                
                                'type_clients_content'=> str_replace('<!--:ru-->','', $content[0]),
                                'type_clients_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                                       
                              ),
                
                       'articles' => array(
                            
                                'type_articles_content' => str_replace('<!--:ru-->','',$content[0] ),  
                                'type_articles_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                                
                                'type_articles_header'=> str_replace('<!--:ru-->','',$title[0] ),
                                'type_articles_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                     
                              ),
                              
                        'news' => array(
                        
                            'type_news_head' => str_replace('<!--:ru-->','', $title[0]),  
                            'type_news_head_uk'=> str_replace('<!--:uk-->','', $title[1]),
                            
                            'type_news_content'=> str_replace('<!--:ru-->','', $content[0]),
                            'type_news_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                   
                              ),
                          
                        'produkciya' => array(
                        
                            'type_produkciya_header' => str_replace('<!--:ru-->','', $title[0]),  
                            'type_produkciya_header_uk'=> str_replace('<!--:uk-->','', $title[1]),
                            
                            'type_produkciya_content'=> str_replace('<!--:ru-->','', $content[0]),
                            'type_produkciya_content_uk'=> str_replace('<!--:uk-->','', $content[1]),
                     
                          )    
                
                );
                
              //  var_dump('Type_Model_'.$v['tree_type']);               
                               
                $typeModelName = 'Type_Model_'.$v['tree_type'];
                
                $typeM = new $typeModelName; 
                
                echo $v['tree_name'].'<br/>';
                
                $typeM->update($typesFields[$v['tree_type']], array('type_'.$v['tree_type'].'_id'=>$v['tree_id'])); 
               
           };
   }  
    */
    
    
?>