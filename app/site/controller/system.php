<?
class Site_Controller_System extends Controller {
    /* {public} */
	
	
	public function sitemapAction(){
	  
		date_default_timezone_set('UTC'); 

		header("Content-Type: text/xml"); 

		$siteurl = Allconfig::$site;
		$sitemap = new K_Sitemap($siteurl);
		
		 // Добавляем главную страницу
		$sitemap->addUrl('',$update,'monthly','0.9');
			  
		$noInSitemap = array('baseblocks','404','index','search','articlepage','object','print','articles','sectionsonmain');   
				
		$removeLang = allconfig::$defoultLang;
				
		//страницы
		$items = K_TreeQuery::crt("/pages/")
									->types('page')
									->go(); 
		
		foreach($items as $v){
			if(!in_array($v['tree_name'], $noInSitemap)){
				$v['tree_link'] = str_replace('/pages', '', $v['tree_link']);
			
				$sitemap->addUrl(rtrim(str_replace('/'.$removeLang, '', $v['tree_link']), '/'),$update,'monthly','0.5');
			}
		}	
				
		//Статьии
		$items = K_TreeQuery::crt("/articles/")
									->types('articles')
									->go(); 
		
		foreach($items as $v){
		
			$v['tree_link'] = str_replace('/articles', '', $v['tree_link']);
			
			$sitemap->addUrl(rtrim(str_replace('/'.$removeLang, '', $v['tree_link']), '/'),$update,'monthly','0.5');
		
		}		
		
		//Продукция с разделами 
		$items = K_TreeQuery::crt("/products/")
									->types('product, productcategory')
									->go(); 
		
		foreach($items as $v){
		
			$v['tree_link'] = str_replace('/products', '', $v['tree_link']);
			
			$sitemap->addUrl(rtrim(str_replace('/'.$removeLang, '', $v['tree_link']), '/'),$update,'monthly','0.5');
		
		}		
				
		$this->putAjax($sitemap->genSitemap());
	  
	}	
	
}