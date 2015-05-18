<?php
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('UTC'); 

    header("Content-Type: text/xml"); 

    $siteurl = K_Registry::get('site');
    $sitemap = new K_Sitemap($siteurl);
	
     // Добавляем главную страницу
    $sitemap->addUrl('/',$update,'monthly','0.9');
	      
    $noInSitemap = array('baseblocks','404','index','search','articlepage','object','print','articles');   
    
	//Страницы с контентом
	$pages = k_q::query('SECELET * FROM content', true);
	
	foreach($pages as $v){
		if($v['alias']){
			$sitemap->addUrl('/'.$v['alias'],$update,'monthly','0.5');
		}
	}
	
	//Страницы с новостями 	  
	$news = k_q::query('SECELET * FROM news', true);
	  
	foreach(news as $v){
		
		$sitemap->addUrl('/news/'.$v['id'] ,$update, 'monthly','0.5');
		 
	}  
	  
    //Страницы по городам
    $sityPages = K_TreeQuery::crt("/sity-page/")
                                ->types('city,nedvijimost')
                                ->go(); 
	
	foreach($sityPages as $v){
		
		$sitemap->addUrl(rtrim(str_replace('/sity-page', '', $v['tree_link']), '/'),$update,'monthly','0.5');
	
	}		

	// обьекты 
 	$obj = k_q::query('SECELET * FROM ads', true);
	
	foreach($sityPages as $v){
		
		$sitemap->addUrl('/ads-'.$v['id'], $update, 'monthly', '0.5');
	
	}
	
	/*	
		// визитки компаний
		$servs = k_q::query('SECELET * FROM services_cont', true);
		
		foreach($sityPages as $v){
			
			$sitemap->addUrl('/ads-'.$v['id'], $update, 'monthly', '0.5');
		
		}	
	*/	 
	
	//	$sitemap->addDirectoryPref('/uk');	
        
    echo  $sitemap->genSitemap();
  
    $this->disableLayout = true;
    exit();    
?>