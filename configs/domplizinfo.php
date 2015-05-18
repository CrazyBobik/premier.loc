<?php
     
    // Define path to aplication folders 
 
     defined('LIB_PATH')
         || define('LIB_PATH', ROOT_PATH.'/www/libraries');   
     defined('BUILDCONFIGS_PATH')
         || define('BUILDCONFIGS_PATH', ROOT_PATH.'/app/dev/buildconfigs');   

class DevConfig{
        public static $modules = array();

        public static $controllers = array();
        
        public static $models = array();
              
        public static $middleNav = array(
            
            'CRUD' =>array('class'=>'iAll',
                                'icon'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAMAAADX9CSSAAAAdVBMVEUAAABFRUViYmJGRkZcXFxra2tkZGRKSkpFRUVXV1dqampHR0dbW1tqampFRUVnZ2dXV1dXV1doaGhra2tgYGBra2tra2tmZmZra2toaGhVVVVFRUVhYWFHR0dqampra2tFRUVcXFxnZ2dZWVlgYGBVVVVGRkZ1kZl5AAAAH3RSTlMA1QkV1dXVD5l4cL2AfoGD4fPxm5bnv2b4Ib9p+Of+oRYskgAAAH5JREFUKM+tzEkOgzAQRNEKzswMIXPUYEPuf0Qkt7Cg1QsWvE1Jf1HYStZqMjia3Cs/Tf2syYGCU+7ngRdymveLn7eBKZSeALhpPfqo3f2HthO9OTASPQK7iv7bsUH06sjsyp/4zErRv3u2+DGJ/hOX4acgpKH3lvqO2RQbGQHSwSNlIkzAOgAAAABJRU5ErkJggg==', 
                                'title'=>'CRUD',
                                'href'=>'/dev/addcrud'
             ),
            'Компоненты' =>array('class'=>'iAll',
                                    'icon'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAMAAADX9CSSAAAAV1BMVEUAAABFRUVra2tra2tkZGRRUVFiYmJkZGRRUVFFRUVFRUVqampRUVFlZWVMTExkZGRFRUVeXl5paWlkZGRQUFBra2tFRUVVVVVra2tVVVVNTU1kZGRiYmLZbJlbAAAAGHRSTlMAZmYz1UrK3FnVM9biTySqqtzK529vSFDEBRQ1AAAAiElEQVQoz63QORKDMBBEUdlGCKyFnUIe7n9O9ywpmX70qkvBlNxj5aUN8GAu7KolOJnlzXV8UAxwiMzj0t071MveM33jPY3olv1mJtl97Lg4wZPZu3adozbDs/mEB9o6RAu8EHMjvTM7tMqdKzM3/of9i4LsgbnbXn+IZCdmlb28tQxnc3FP/QH7BQtrqdcp6wAAAABJRU5ErkJggg==', 
                                    'title'=>'Компоненты',
                                    'href'=>'/dev/addcontroller'
             ),
            'Разработка' =>array('class'=>'iAll',
                                    'icon'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAMAAADX9CSSAAAAV1BMVEUAAABFRUVra2tra2tkZGRRUVFiYmJkZGRRUVFFRUVFRUVqampRUVFlZWVMTExkZGRFRUVeXl5paWlkZGRQUFBra2tFRUVVVVVra2tVVVVNTU1kZGRiYmLZbJlbAAAAGHRSTlMAZmYz1UrK3FnVM9biTySqqtzK529vSFDEBRQ1AAAAiElEQVQoz63QORKDMBBEUdlGCKyFnUIe7n9O9ywpmX70qkvBlNxj5aUN8GAu7KolOJnlzXV8UAxwiMzj0t071MveM33jPY3olv1mJtl97Lg4wZPZu3adozbDs/mEB9o6RAu8EHMjvTM7tMqdKzM3/of9i4LsgbnbXn+IZCdmlb28tQxnc3FP/QH7BQtrqdcp6wAAAABJRU5ErkJggg==', 
                                    'title'=>'Разработка',
                                    'href'=>'/dev/backup'
             ),
             'Тесты' =>array('class'=>'iAll',
                                    'icon'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAXCAMAAADX9CSSAAAAV1BMVEUAAABFRUVra2tra2tkZGRRUVFiYmJkZGRRUVFFRUVFRUVqampRUVFlZWVMTExkZGRFRUVeXl5paWlkZGRQUFBra2tFRUVVVVVra2tVVVVNTU1kZGRiYmLZbJlbAAAAGHRSTlMAZmYz1UrK3FnVM9biTySqqtzK529vSFDEBRQ1AAAAiElEQVQoz63QORKDMBBEUdlGCKyFnUIe7n9O9ywpmX70qkvBlNxj5aUN8GAu7KolOJnlzXV8UAxwiMzj0t071MveM33jPY3olv1mJtl97Lg4wZPZu3adozbDs/mEB9o6RAu8EHMjvTM7tMqdKzM3/of9i4LsgbnbXn+IZCdmlb28tQxnc3FP/QH7BQtrqdcp6wAAAABJRU5ErkJggg==', 
                                    'title'=>'Тесты',
                                    'href'=>'/dev/backup'
             ),
              
        );  
        
        /*
        CRUD
        
        Операция	SQL-оператор	Операция в HTTP
        Создание (Create)	INSERT	POST
        Чтение (Read)	SELECT	GET
        Редактирование (Update)	UPDATE	PUT или PATCH
        Удаление (Delete)	DELETE	DELETE
        
        */
        
        public static $menuTabs = array(
        
                  'Генератор CRUD' =>array('controllers'=>array('addcrud', 'addcrudtype'),
                                                       
                                                         'menuTabs'=>array(   
                                                                         'addcrud'=> array('title'=>'Генератор CRUD',
                                                                                        'href'=>'/dev/addcrud'
                                                                                        ),
                                                                         'addcrudtype'=> array('title'=>'Генератор CRUD для типов',
                                                                                        'href'=>'/dev/addcrudtype'
                                                                                       )
                                                                      )
                                                         ),
                                                        
                  'Генератор компонентов' =>array('controllers'=>array('addcontroller', 'addmodel', 'addhmvc', 'addsimpleblock'),
                                                       
                                                         'menuTabs'=>array(
                                                            
                                                                         'addcontroller'=> array('title'=>'Контроллер',
                                                                                        'href'=>'/dev/addcontroller'
                                                                                         ),
                                                                         'addmodel'=> array('title'=>'Модель',
                                                                                        'href'=>'/dev/addmodel'
                                                                                       ),
                                                                         'addhmvcblock'=> array('title'=>'HMVC блок',
                                                                                        'href'=>'/dev/addhmvcblock'
                                                                                       ),
                                                                         'addsimpleblock'=> array('title'=>'Простой блок',
                                                                                        'href'=>'/dev/addhmvcblock'
                                                                                       )
                                                                         
                                                                       )
                                                        ),
                  'Разработка' =>array('controllers'=>array('debug', 'backup', 'profiler','eval','request'),
                                                       
                                                         'menuTabs'=>array(   
                                                                         'debug'=> array('title'=>'Дебаг',
                                                                                         'href'=>'/dev/debug'
                                                                                         ),
                                                                                         
                                                                         'backup'=> array('title'=>'Бекап',
                                                                                          'href'=>'/dev/backup'
                                                                                       ),
                                                                                         
                                                                         'profiler'=> array('title'=>'Профайлер',
                                                                                          'href'=>'/dev/profiler'
                                                                                       ),
                                                                                           
                                                                         'eval'=> array('title'=>'Выполнить',
                                                                                          'href'=>'/dev/eval'
                                                                                       ),
                                                                                        
                                                                         'requests'=> array('title'=>'Запросы',
                                                                                          'href'=>'/dev/requests'
                                                                                       )
                                                                      )
                                                        ), 
                                                         
                  'Тесты' =>array('controllers'=>array('testconfig', 'testhtacces', 'testunit', 'testmysql', 'ativirus', 'testinstall'),
                                               
                                                 'menuTabs'=>array(   
                                                                 'testconfig'=> array('title'=>'Проверка конфига',
                                                                                    'href'=>'/dev/testconfig'
                                                                                 ),
                                                                                 
                                                                 'testhtacces'=> array('title'=>'Проверка htacces',
                                                                                  'href'=>'/dev/testhtacces'
                                                                               ),
                                                                                 
                                                                 'testunit'=> array('title'=>'Unit',
                                                                                  'href'=>'/dev/testunit'
                                                                               ),
                                                                 'testfunct'=> array('title'=>'Функц.',
                                                                                  'href'=>'/dev/testunit'
                                                                               ),
                                                                                   
                                                                 'testmysql'=> array('title'=>'Тесты mysql',
                                                                                  'href'=>'/dev/testmysql'
                                                                               ),
                                                                 'testinstall'=> array('title'=>'Проверка',
                                                                                  'href'=>'/dev/testinstall'
                                                                               ),
                                                              )
                                                )          
                                          
        );  
       
        public static $crudTables = array('users' =>array(      'alias' => 'u',
                                                                'fields'=>array( 'id'=> array(  'width'=>'50',
                                                                                                'type'=>"int",
                                                                                                'type'=>"int"
                                                                                              ),
                                                                                  'mail'=> array( 'width'=>'120',
                                                                                                  'type'=>"int",
                                                                                                  'type'=>"int"
                                                                                              ),
                                                                                  'name'=> array( 'width'=>'120',
                                                                                                  'type'=>"int",
                                                                                                  'type'=>"int"
                                                                                                
                                                                                              ),
                                                                                  'fam'=> array( 'width'=>'120',
                                                                                                 'type'=>"int",
                                                                                                 'type'=>"int"
                                                                                                
                                                                                              ),
                                                                                  'balans'=> array( 'width'=>'50',
                                                                                                    'type'=>"int",
                                                                                                    'set'=>"between",
                                                                                                    
                                                                                                  ),
                                                                                  'pkt'=> array( 'width'=>'120',
                                                                                                 'type'=>"int",
                                                                                                 'set'=>"add",
                                                                                                 'otions'=>array(
                                                                                                            'table'=>'pkt',
                                                                                                            'value'=>'id',
                                                                                                            'title'=>'title'
                                                                                                 )
                                                                                               ),
                                                                                  'avatar'=> array( 'width'=>'100',
                                                                                                    'type'=>"string",
                                                                                                 
                                                                                  
                                                                                                  ),
                                                                                  'date'=> array( 'width'=>'120',
                                                                                                  'type'=>"TIMESTAMP",
                                                                                                  'set'=>"between",
                                                                                                  )        
                                                                )   
                                                        )
        );  
        
        // разные установки, которые используют дефайны              
        static function set(){
                       
            foreach(K_File::rdir(APP_PATH) as $v){
                if(is_dir(APP_PATH.'/'.$v) && (!in_array($v, array('plugins')))){
                    DevConfig::$modules[] = $v;
                }
    		}
        
            foreach(K_File::rdir(K_PATH.'/controller') as $v){
                    DevConfig::$controllers[] = str_replace('.php','',$v);
          	}
            
            foreach(DevConfig::$modules as $f){
            
                foreach(K_File::rdir(APP_PATH.'/'.$f.'/model') as $v){
                        DevConfig::$models[$f][] = str_replace('.php','',$v);
              	}
                 
            }
        
        }              
  
}

DevConfig::set();  
  
?>