<?php
class K_Sitemap{
   
   public  $sitemapUrls = array() ;
   private $site = '/'; // если язык реализован через директорию
   private $directoryPrefs = array(''); //префикс директоря например для языков реализованных через директорию. Обязательно должен быть пустой элемент
   private $sitemapHead = '<?xml version="1.0" encoding="utf-8" ?>';
   private $sitemapUrlset = '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"  xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
   private $sitemapUrlsetClose = '</urlset>';

    private function lastmodPage($lastmod){
      
    	if(!$lastmod){
          return  date("Y-m-d").'T'.date("h:i:s",time()-3600).'+00:00'; ;
    	}
      
          return $lastmod;
    }
     
     
    public function __construct($site){
      
    	$this->site = $site;
    	
    }
    
    public function setSite($site){
      
    	$this->site = $site;
    	
    }
  	 
   public function addUrl($url, $lastmod, $changefreq = "daily", $priority ="0.5" ){
    
   		$urlArray = array('url'=>$url,
						  'lastmod'=>$lastmod, 
						  'changefreq'=>$changefreq,
						  'priority'=>$priority
		);
		
        $this->sitemapUrls[] = $urlArray; 
             
   }
   
    public function genUrl($url, $lastmod, $changefreq = "daily", $priority ="0.5", $pref='' ){
    
		return   "<url> 
					<loc>".$this->site.$pref.$url."</loc> 
					<lastmod>".$this->lastmodPage($lastmod)."</lastmod> 
					<changefreq>$changefreq</changefreq> 
					<priority>$priority</priority> 
				   </url>"; 
             
    }
   
   
    public function addDirectoryPref($pref){
       
		$this->directoryPrefs[] = $pref;
       
    }
   
   
   public function genSitemap(){
  
		foreach($this->directoryPrefs as $t){
			
			foreach($this->sitemapUrls as $v){
			
				$urlsXmlArr[]= $this->genUrl($v['url'],$v['lastmod'],$v['changefreq'],$v['priority'],$t);
				
			}
		
		}
		
        return  $this->sitemapHead."\n".
                $this->sitemapUrlset."\n".
                implode("\n" ,$urlsXmlArr)."\n".
			    $this->sitemapUrlsetClose; 
       
   }
   
}