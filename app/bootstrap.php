<?php 

/**
 * Bootstrap
 */

spl_autoload_register(array('K_Loader', 'auto_load'));

include K_PATH.'functions.php';
include LIB_PATH.'/db.php';
include LIB_PATH.'/functions.php';

class Bootstrap implements K_Application_IBootstrap {

	protected $application;
		
	public $route;
        
    public $cacheManager = null;
   
	public $db = null;
	
	public function __construct(){
		   			
            if($_SERVER['REMOTE_ADDR']=='95.135.123.100' || $_SERVER['REMOTE_ADDR']=='195.138.90.140' || $_SERVER['REMOTE_ADDR']=='127.0.0.1' || ( $ips[0] == '192' && $ips[1] == '168' )){

//               K_Debug::get()->enable(true);

            }else{
						
				// K_Debug::get()->enable(false);
			}	
				 
			K_Debug::get()->enable(false);
				 
            $this->application = K_Application::get();

            K_Registry::write( 'bootstrap', $this );
            	
	}
    
	public function getApplication() {
	   
            return $this->application;
            
	}
	
	public function run() {

            $this->loadOptions();

            $this->initCache();

            $this->initDB();

	       	//$this->initAccess();
			
            //$this->initTranslate();	

            $this->initPlugins();

            $this->initRouter();
            
            $this->initPreferences();
	}
    
        
    public function initPreferences() {
            
    }
        
    public function initPlugins() {
            K_Plugins::addDirectory( APP_PATH.'/plugins' );
    }
		
	public function initAccess() {
            K_Access::load(true);
    }	
    
    /** @method B loadOptions() загрузка настроек
     * 
     */        
	
	public function loadOptions() {
	   
            $salt = '123asd...!!!eee';
            K_Registry::write('Configure.salt', $salt );
                    
            $domenName = AllConfig::$domen;
            K_Registry::write('domen', $domenName);
            
            $site = 'http://'.$domenName;
            K_Registry::write('site', $site);
            
 	}
    
    
    /** @method B initCache() инициализация кеша
     * 
     */    
	
	public function initCache() {
	
			$cacheTableStructure = $this->cacheManager = new K_Cache_Manager( '5min', array(
                    'cache_dir' => VARS_PATH.'/cache/5min',
                    'lifetime' => 300 // sec
            ) );
            
            $cacheTableStructure = $this->cacheManager = new K_Cache_Manager( 'table_structure', array(
                    'cache_dir' => VARS_PATH.'/cache/table_structure',
                    'lifetime' => 60 // @todo Кэш структур таблиц (DESCRIBE) после финала увеличить цифру, минимум до 1 часа
            ) );

            $cacheSql = $this->cacheManager = new K_Cache_Manager( 'sql', array(
                    'cache_dir' => VARS_PATH.'/cache/sql',
                    'lifetime' => 86400
            ) );

            $cacheUnlim = $this->cacheManager = new K_Cache_Manager( 'unlim', array(
                    'cache_dir' => VARS_PATH.'/cache/unlimited',
                    'lifetime' => 100 // @todo change to 99999999999999
            ) );
            
             $cacheUnlim = $this->cacheManager = new K_Cache_Manager( '24h', array(
                    'cache_dir' => VARS_PATH.'/cache/24h',
                    'lifetime' => 86399 
            ) );

            /*$cacheMemcached = $this->cacheManager = new K_Cache_Manager( 'memcached', array(
                    'class' => K_CACHE_MEMCACHED,
                    'lifetime' => 24*60*60
            ) );*/

            K_Registry::set('cacheManager', $this->cacheManager );
	}
    
     /** @method B initDB() Инициализация базы данных 
     * 
     */
 	
	public function initDB() {
		        
            $this->db = new K_Db_Adapter( array(        
                    'host' => AllConfig::$mysqBDConf['host'],
                    'user' => AllConfig::$mysqBDConf['user'],
                    'password' =>AllConfig::$mysqBDConf['password'],
                    'database' => AllConfig::$mysqBDConf['database'],
                    'structureCache' => $this->cacheManager->get('table_structure')
            ) );

            K_Registry::set( 'db', $this->db );
	}
    
    
    /** @method B initTranslate() Инициализация перевода в зависимости от поддомена
     *  @
     */
    	
	public function initTranslate() {
	   
               $num = 0;
               $siteDomen = parse_url($_SERVER['HTTP_HOST']);
               $podDomen = explode(".", $siteDomen["path"]);
               
               if ($podDomen[$num] == "www") {
                   $langDomen =  $podDomen[++$num];
               }
                            
               $langDomen = $podDomen[$num];
        
               if($langDomen == 'en'){
               
                  $Arrx8yrtge1='en';
                  K_Registry::set('lang',$Arrx8yrtge1);
                  K_Locale::set('en_US');

               }else{
               
                  $Arrx8yrtge1='ua';
                  K_Registry::set('lang',$Arrx8yrtge1);
                  K_Locale::set('uk_UA');
              
               }  
               
             if( K_Registry::get('lang') =='en'){
                $link .="/". K_Registry::get('lang');
                $addlang =  K_Registry::get('lang').'/';
              }
          
            K_Translate::setDirectory( ROOT_PATH.'/translate' );
	}
	
    /** @method B initRouter() Инициализация роутера, описание роутеров находиться в конфигах
     * 
    */
    
	public function initRouter(){
	         
           require_once ROOT_PATH.'/configs/routers/indexrouter.php';
	
           K_Request::addRewriteRule( new IndexRouter() ); // set index router
		
	}

}

?>