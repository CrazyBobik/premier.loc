<?php
     
    // Define path to aplication folders 
 
     defined('LIB_PATH')
         || define('LIB_PATH', ROOT_PATH.'/www/libraries');   

class AdminConfig{
              
        public static $middleNav = array(
        
            'На сайт' =>array(
							  'class'=>'iAll',
                              'icon'=>'/adm/img/admin/site.png', 
                              'title'=>'На сайт',
                              'href'=>'/',
                              'target'=>'_blank'
                                    
							),
             
            'Перезагрузить' =>array(
									'class'=>'iAll',
                                    'icon'=>'/adm/img/admin/reload.png', 
                                    'title'=>'Перезагрузить',
                                    'href'=>'javascript:window.location.reload();',
                                   
								)
    
        );  
        
        public static $menuTabs = array(
                                 
       
				  'Персонал' =>array('controllers'=>array('acl'),
												   
													 'menuTabs'=>array(   
													 
																	 'acl'=> array('title'=>'Сотрудники',
																					 'href'=>'/admin/acl'
																					 ),
																					 
																	 'roles'=> array('title'=>'Заявки на смену пакета',
																					  'href'=>'/admin/acl/roles'
																					 ),
													
																  )
													),				
									
        );
        
        // config настроек 
        public static $settingsConfig = array(
                
        
        );
               
        public static $crudTables = array();
                                            
        // разные установки, которые используют дефайны              
        static function set(){
            
            require_once CONFIGS_PATH.'/admin_cruds.php';
         	self::$crudTables = &$crudTables;
        
        }              
  
}
  
AdminConfig::set();  
  
?>