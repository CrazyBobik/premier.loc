<?php
     
    // Define path to aplication folders 
    
    defined('WWW_PATH')
        || define('WWW_PATH', realpath(ROOT_PATH.'/www'));
    
    defined('APP_PATH')
        || define('APP_PATH', ROOT_PATH . '/app');
	    
    defined('PLATFORM_PATH') 
    	|| define('PLATFORM_PATH', ROOT_PATH . '/framework');
    	
    defined('CONFIGS_PATH') 
    	|| define('CONFIGS_PATH', ROOT_PATH . '/configs');    
        
    defined('K_PATH') 
    	|| define('K_PATH', ROOT_PATH . '/framework/k');
    
    defined('UPLOAD_PATH') 
    	|| define('UPLOAD_PATH', WWW_PATH.'/upload');    
               
    defined('CHUNK_PATH')
         || define('CHUNK_PATH', APP_PATH.'/site/_chunk' );
         
    defined('VARS_PATH')
        || define('VARS_PATH', ROOT_PATH.'/vars'); 
             
    defined('BACKUP_PATH')
        || define('BACKUP_PATH', VARS_PATH.'/backups');   
        
    defined('CACHE_PATH')
        || define('CACHE_PATH', VARS_PATH.'/cahce');   
     
    defined('TEMP_PATH')
        || define('TEMP_PATH', VARS_PATH.'/temp'); 
		
	defined('CLI_PATH')
	 || define('CLI_PATH', ROOT_PATH.'/cli' );  	
  
class AllConfig{

    static public $domen='premier.loc';

    static public $mysqBDConf = array(

        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'database' => 'premier.loc'

    );
                    
        static public $mysqlDump = array(
       
                        'link'=>'testdump',
                        'secureTokenArg'=>'token', 
                        'secureToken'=>'elinokoll786', 
                        'insertRecordsCount'=>50
			           
					 ); 
   				 
		static public $defoultLang = 'ru'; 
		  
		static public $siteLang; 
		
		static public $contentLang; 			
		  
		  
         // массив роутеров 	
		
        /*
        
       'parserule'=> '/^\/(?P<module>[a-z0-9_-]+)?(\/(?P<controller>[a-z0-9_-]+)(\/(?P<action>[a-z0-9_-]+)(\/(?P<params>.*)?)?)?)?/is',
													
       <module>
       <controller>
       <action>
       
       <params>
        
        */
        //  index-
        
        /**
         * Routes are used to determine the controller and action for a requested URI.
         * Every route generates a regular expression which is used to match a URI
         * and a route. Routes may also contain keys which can be used to set the
         * controller, action, and parameters.
         *
         * Each <key> will be translated to a regular expression using a default
         * regular expression pattern. You can override the default pattern by providing
         * a pattern for the key:
         *
         *     // This route will only match when <id> is a digit
         *     Route::set('user', 'user/<action>/<id>', array('id' => '\d+'));
         *
         *     // This route will match when <path> is anything
         *     Route::set('file', '<path>', array('path' => '.*'));
         *
         * It is also possible to create optional segments by using parentheses in
         * the URI definition:
         *
         *     // This is the standard default route, and no keys are required
         *     Route::set('default', '(<controller>(/<action>(/<id>)))');
         *
         *     в скобочках указаны не обязательные ключи 
         * 
         *     // This route only requires the <file> key
         *     Route::set('file', '(<path>/)<file>(.<format>)', array('path' => '.*', 'format' => '\w+'));
         *
         * 
         * Routes also provide a way to generate URIs (called "reverse routing"), which
         * makes them an extremely powerful and flexible way to generate internal links.
         */
               
    static public $routes = array(
        'sitemap'=> array(
            'url'=> 'sitemap.xml',
    													
            'valids'=>array(
                'controller'=>'.*',
                'action'=>'.*',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array(
                'module'=>'site',
                'controller'=>'system',
                'action'=>'sitemap',
                'params'=>array()
                                                                         
            ),
                                                          
            'get'=>'all',// validonly, none
            'params'=>'all'// validonly, none
                                                      
        ),
        'debugoutput'=> array(
            'url'=>'debugoutput',
    													
            'valids'=>array(
                'controller'=>'.*',
                'action'=>'.*',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array(
                'module'=>'site',
                'controller'=>'system',
                'action'=>'debugoutput',
                'params'=>array()
                                                                         
            ),
                                                          
            'get'=>'all',// validonly, none
            'params'=>'all'// validonly, none
                                                      
        ),
        'ajax'=> array(
            'url'=> 'ajax/<controller>/<action>(/<params>)',
    													
            'valids'=>array(
                'controller'=>'[a-z0-9_-]+',
                'action'=>'[a-z0-9_-]+',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array(
                'module'=>'ajax',
                'controller'=>'index',
                'action'=>'index',
                'params'=>array()
                                                                         
            ),
                                                          
            'get'=>'all',// validonly, none
            'params'=>'all'// validonly, none
                                                      
        ),
                                                     
        'api' => array(
            'url'=> 'api/<controller>/<action>(/<params>)',
    													
            'valids'=>array(
                'controller'=>'[a-z0-9_-]+',
                'action'=>'[a-z0-9_-]+',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array(
                'module'=>'api',
                'controller'=>'index',
                'action'=>'index',
                'params'=>array()
                                                                         
            ),
                                                          
            'get'=>'all',// validonly, none
            'params'=>'all'// validonly, none
                                                    
        ),
                                      
        'admin' => array(  'url'=> 'admin(/<controller>(/<action>(/<params>)))',
    													
            'valids'=>array(
                'controller'=>'[a-z0-9_-]+',
                'action'=>'[a-z0-9_-]+',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array(
                'module'=>'admin',
                'controller'=>'index',
                'action'=>'index',
                'params'=>array()
                                                                         
            ),
                                                          
            'get'=>'all',// validonly, none
            'params'=>'all',// validonly, none
            'loadconfigs'=>array('admin_config'),
            'loaderError'=>'/admin/404/', // редирект если в лоудере ошибка
            'debug'=>true // дебаг для кждой записи в роутере
        ),
                                                         
        'dev' => array(  'url'=> 'dev(/<controller>(/<action>(/<params>)))',
    													
            'valids'=>array(
                'controller'=>'[a-z0-9_-]+',
                'action'=>'[a-z0-9_-]+',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array(
                'module'=>'dev',
                'controller'=>'index',
                'action'=>'index',
                'params'=>array()
            ),
                                                          
            'get'=>'all',// validonly, none
            'params'=>'all',// validonly, none
            'loadconfigs'=>array('domplizinfo'),
            'loaderError'=>'/admin/404/',
            'debug'=>true // дебаг для каждой записи в роутере
                                                     
        ),
                                                       
        'treerouter'=> array('url'=>'<treeurl>',
                                       
            'valids'=>array('treeurl'=>'.*'
                                                            
            ),
                                                             
            'defaults'=>array('module'=>'site',
                'controller'=>'index',
                'action'=>'page',
                'params'=>array(),
            ),
                                                             
            'get'=>'all'// all, validonly, none  // валидация гет происходит внутри треероутера
                                                           
        ),
                                                    
        'site'=> array(   'url'=>'(<controller>(/<action>(/<params>)))', // оставил для примера.
                                       
            'valids'=>array('controller'=>'[a-z0-9_-]+',
                'action'=>'[a-z0-9_-]+',
                'params'=>'.*',
            ),
                                                          
            'defaults'=>array('module'=>'site',
                'controller'=>'index',
                'action'=>'index',
                'params'=>array(),
            ),
											      
            //валедирование гет запроса
            'getvalids'=>array('site/index/index'=>'none',
                'site/reg/index'=>'none',
                'site/reg/restore'=>array('rl'=>array('len'=>32
                                                                             
                )
                )
                                                                    
            ),
                                                      
            'paramsFormat'=>array('/<key>:<value>/'), // '/<key>:<value>/'
                                                            
            'paramsvalids'=>array(
                                                                       
            ),
                                                           
            'params'=>'all',// validonly, none
        )
    );
      
    static public $adsImgPaths = array();
		
    static public $sr = array();
				
    static public $site;

    // разные установки, которые используют дефайны
    static function set(){
            
        self::$objImgPaths['temp'] = TEMP_PATH.'/';
        self::$objImgPaths['original'] = BACKUP_PATH."/original_images/";
        self::$objImgPaths['big'] =  WWW_PATH."/upload/objects/";
        self::$objImgPaths['thumb'] = self::$objImgPaths['big'].'/thumb/';
        self::$objImgPaths['watermark'] = WWW_PATH."/img/system/w.png";
        self::$objImgPaths['watermarkImport'] = WWW_PATH."/img/system/w_i.png";
			  
        self::$site = 'http://'.self::$domen;
           
    }

}

AllConfig::set();  

?>