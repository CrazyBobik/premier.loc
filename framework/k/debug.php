<?php 

/**
 * Class Debug
 * 
 * <example>
 * $model = new K_Db_Model();
 * $string = $model->name;
 * K_Debug::get()->enable( true );
 * K_Debug::get()->dump( $model );
 * K_Debug::get()->dump( $string );
 * K_Debug::get()->addMessage( '--- complete ---' );
 * K_Debug::get()->printAll();
 * K_Debug::get()->enable( false );
 * </example>
 */

class K_Debug {
    
	public $sqlList = array();
	public $errorList = array();
	public $messageList = array();
	public $dumpList = array();
	
	protected $elapse = 0; // start time
	
	protected static $instance = null;
	protected $enabled = true;
	
	protected static $copy = 0;
	public $myCopy = -1;
	
	protected function __construct() {
	  // require_once (WWW_PATH . '/ExtProce/debug/xhprof/xhprof_lib/utils/xhprof_lib.php');
      // require_once (WWW_PATH . '/ExtProce/debug/xhprof/xhprof_lib/utils/xhprof_runs.php');  
      //  xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);  
     
		$this->enabled = true;
		self::$instance = $this;
		$this->elapse = K_Time::microtime_float();
	}
	public function isEnabled() {
	  return $this->enabled; 
    }
	// Enable or disable debug
	public function enable( $enable = true ) {
	   
		$this->enabled = $enable;
        
        if($this->enabled == true){
            
            if (!function_exists('vd'))
            {
                /**
                 * var_dump()
                 */
                function vd($string)
                {
                    var_dump($string);
                }
            }
            
            if (!function_exists('pr'))
            {
                /**
                 * print_r($string, $return)
                 */
                function pr($string, $return = FALSE)
                {
                    if ($return)
                    {
                        return print_r($string, TRUE);
                    }
                    else
                    {
                        print_r($string);
                    }
                }
            }
            
            if (!function_exists('vdd'))
            {
                /**
                 * var_dump() + die()
                 */
                function vdd($string)
                {
                    var_dump($string);
                    exit();
                }
            }
            
            if (!function_exists('prd'))
            {
                /**
                 * print_r($string, $return) + die()
                 */
                function prd($string, $return = FALSE)
                {
                    if ($return)
                    {
                        return print_r($string, TRUE);
                    }
                    else
                    {
                        print_r($string);
                    }
                    exit();
                }
            }
            
            if (!function_exists('gcm'))
            {
                /**
                 * get_class_methods($class)
                 */
                function gcm($class)
                {
                    return get_class_methods($class);
                }
            }
            
            if (!function_exists('e'))
            {
                /**
                 * echo($string)
                 */
                function e($string)
                {
                    print($string);
                }
            }
            
            if (!function_exists('d'))
            {
                /**
                 * die($string)
                 */
                function d($string = NULL)
                {
                    die($string);
                }
            }
           
        }
       
	}
	
	// Add SQL string message
	public function addSql( $text, $numRows = "N/A", $duration = "N/A" ) {
		$this->sqlList[] = 
			'<tr><td>'.(count($this->sqlList)+1).'. '.htmlspecialchars($text).'</td>'.
			'<td>'.$numRows.'</td><td>'.$duration.'</td></tr>';
	}
	
	// Add Error string message
	public function addError( $text ) {
		$this->errorList[] = (count($this->errorList)+1).'. '.htmlspecialchars($text).'<br/><br/>';
	}
	
	// Add simple message
	public function addMessage( $text ) {
		$this->messageList[] = (count($this->messageList)+1).'. '.htmlspecialchars($text).'<br/><br/>';
	}
	
	// Add object for dump
	public function dump( &$object, $name = null ) {
		$this->dumpList[] = array( 'object' => $object, 'name' => $name );
	}
	
	// Print all debug log to the screen
	public function printAll() {
		if ( !$this->enabled ) return;
		
		$html = '';
		if ( count($this->errorList) ) {
			$html .= '<strong style="color:red">Errors:</strong><br/>';
			foreach( $this->errorList as &$string ) {
				$html .= $string;
			}
			$html .= '<hr/>';
		}
		
		if ( count($this->messageList) ) {
			$html .= '<strong style="color:green">Messages:</strong><br/>';
			foreach( $this->messageList as &$string ) {
				$html .= $string;
			}
			$html .= '<hr/>';
		}
		
		if ( count($this->sqlList) ) {
		
			$strongList = array(
				'SELECT',
				'WHERE',
				'ORDER BY',
				'FROM',
				'GROUP BY',
				'HAVING',
				'LIMIT',
				'OFFSET',
				'AND',
				'OR',
				'NOT',
				'UPDATE',
				'INSERT INTO',
				'DUPLICATE KEY',
				'ON',
				'VALUES',
			);
		
			$html .= '<strong style="color:black">SQL:</strong><br/><table style="font-size:12px">'.
				 '<tr><th>Sql query</th><th>Return rows</th><th>Elapse</th></tr>';
			foreach( $this->sqlList as &$string ) {
				foreach( $strongList as $strongText ) {
					$string = str_replace( $strongText.' ', '<strong>'.$strongText.' </strong>', $string);
				}
				$html .= $string;
			}
			$html .= '</table><hr/>';
		}
		
		if ( count($this->dumpList) ) {
			$html .= '<strong style="color:blue">Data dumps:</strong><hr/>';
			$i = 1;
			foreach( $this->dumpList as &$item ) {				
				$html .= $i++.'.'.($item['name']?'<strong>'.$item['name'].'</strong>':'').'<hr/>';
				var_dump( $item['object'] );
				$html .= '<hr/>';
			}
		
		}
		
		$elapse = K_Time::microtime_float() - $this->elapse;
		$html .= 'Render time: '.$elapse.' s';
		
      //  $xhprof_data = xhprof_disable();
      // $xhprof_runs = new XHProfRuns_Default();
      //  $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_testing");
      // Формируем ссылку на данные профайлинга и записываем ее в консоль
      //  $link = "http://prof.loc/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";
      //  echo '<br><a  href="'.$link.'">профайлер</a><br><br><br><br><br><br>';
        
	   return $html."<br/><br/>";
	}
	
		// Print all debug log to the screen
	public function printAll2() {
		if ( !$this->enabled ) return;
		
	
		
		
			foreach( $this->sqlList as &$string ) {
			
				$html .= $string."\n";
			}
			
		
				
      //  $xhprof_data = xhprof_disable();
      // $xhprof_runs = new XHProfRuns_Default();
      //  $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_testing");
      // Формируем ссылку на данные профайлинга и записываем ее в консоль
      //  $link = "http://prof.loc/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";
      //  echo '<br><a  href="'.$link.'">профайлер</a><br><br><br><br><br><br>';
        
	   return $html;
	}
    
   	public function printCache($token) {
   	    
         K_Cache_Manager::get('unlim')->save($token, $this->printAll());
         
	}
    
    public function __destruct() {
   	    
         K_Cache_Manager::get('unlim')->save('DBG', $this->printAll());
         
	}   
    
	// Get K_Debug instance
	public static function get() {
		if ( !self::$instance ) {
			return new K_Debug();
		}
		return self::$instance;
	}
    
    // Отправляет сообщение в консоль браузера
	public static function consol($var) {
	   if(is_string($var)){
            echo "<script> console.log('{$var}')</script>";
         }
         else{
            echo  "<script> console.log('".print_r($var,true)."')</script>";
         }   
 	}
    
      // добвляет hTml дебагера в код 
	public function html() {
	 
    	  if(!$this->enabled){
    	     return;
      	  }
          
          $html = '<div id="debug-con">
                        <div id="debug-body"></div>
                        <div id="debug-controls">
                           <ul id="debug-controls-ul">
                               <li><div class="debug-collapse">_</div></li>
                               <li><div class="debug-open">][</div></li>
                               <li style="float:right"><a class="debug-settings" href="/dev/debug">Settings</a></li>
                           </ul>
                        </div>
                   </div>';
                    
          $html .=  '<style type="text/css">
                        #debug-con{
                            
                            width:200px;
                            height:400px;
                            position:fixed;
                            top:0px;
                            right:0px;
                            display:none;
                            z-index:99999;
                            background-color:#fff;
                         
                        }
                        #debug-body{
                             color:#000;
                             padding:0 0 0 5px;
                             height:380px;
                             text-align:left;
                             font-size:9px;
                             width:100%;
                             overflow-y:auto;
                             overflow-x:hidden;
                        }
                        
                        #debug-controls{
                            position:releative;
                            left:0px;
                            bottom:0px;
                            width:100%;
                            height:20px;
                        }
                        #debug-controls-ul li{
                            display:block;
                            float:left;
                            list-style: none;
                            text-align: left;
                            margin:0 0 0 0
                        }
                        #debug-controls-ul li div{
                            color:#000;
                            cursor:pointer;
                            width:19px;
                            height:20px;
                            text-align:center;
                            font:14px bold verdana;
                        }
                        
                        .debug-collapse{
                        
                        }
                        .debug-restore{
                        
                        }
                        .debug-open{
                         
                        }
                        .debug-smaller{
                          
                          
                        }
                        .debug-open:hover{
                            color:#000;
                            background-color:#f1d352;
                            cursor:pointer;
                            
                        }
                        .debug-smaller:hover{
                            color:#000;
                            background-color: #f1d352;
                        }
                        .debug-collapse:hover{
                            color:#000;
                            background-color: #f1d352;
                            cursor:pointer;
                            
                        }
                        .debug-restore:hover{
                            color:#000;
                            background-color: #f1d352;
                            cursor:pointer;
                            
                        }
                        
                        .debug-settings{
                            margin:0 10px 0 0;
                        }
                     
                     </style>';   
                         
          $html .= '<script type="text/javascript">
		  
                       function debugoutput(settings){
                                
                                var reqInfo=\'\'; 
                                var reqInfoArray=[];
                                
                                if(settings !== undefined){
                                    
                                    reqInfoArray.push(\'<em class"">Url</em>:\'+settings.url);
                                    reqInfoArray.push(\'<em class"">Type</em>:\'+settings.type);
                             
                                }
                                
                                reqInfo =    reqInfoArray.join(\'<br/>\');   
                                                                
                                var request = $.ajax({
                                                  url: "/debugoutput",
                                                  type: "GET",
                                                  dataType: "html",
                                                  global:false,
                                                }).done(function( data ){
                                                            
                                                          $("#debug-body").prepend(reqInfo+data);
                                                          $("#debug-con").show();
                                                });
                        }
							
						function debugSet(value){
								
								switch (value) {
								
									case \'open\':
										$(\'.debug-open\').removeClass(\'debug-open\').addClass(\'debug-smaller\').html(\'[]\');
										$(\'.debug-restore,.debug-collapse\').removeClass(\'debug-restore\').addClass(\'debug-collapse\').html(\'_\');
										$(\'#debug-con\').width(800).height(600);
										$(\'#debug-body\').width(800).height(580).css(\'font-size\',\'12px\');
									break

									case \'smaller\':
										$(\'.debug-smaller\').removeClass(\'debug-smaller\').addClass(\'debug-open\').html(\'][\');
										$(\'#debug-con\').width(200).height(400);
										$(\'#debug-body\').width(200).height(380).css(\'font-size\',\'9px\');
									break

									case \'collapse\':
										$(\'.debug-collapse\').removeClass(\'debug-collapse\').addClass(\'debug-restore\').html(\'||\');
										$(\'.debug-smaller,.debug-open\').removeClass(\'debug-smaller\').addClass(\'debug-open\').html(\'][\');
										$(\'#debug-con\').width(18).height(20);
										$(\'#debug-body\').width(0).height(0);
									break

									case \'restore\':
										$(\'.debug-restore\').removeClass(\'debug-restore\').addClass(\'debug-collapse\').html(\'_\');
										$(\'.debug-smaller,.debug-open\').removeClass(\'debug-smaller\').addClass(\'debug-open\').html(\'][\');
										$(\'#debug-con\').width(200).height(400);
										$(\'#debug-body\').width(200).height(380);
									break

								}

						}
						
                        $(function(){
						
							var debugStait = getCookie(\'debug\');
							debugSet(debugStait);
                        
							debugoutput();
                            
                            $( document ).ajaxComplete(function( event, request, settings ) {
                                console.debug(settings);
                                debugoutput();
                            }); 
                            							
                            $( document ).on(\'click\',\'.debug-open\',function() {
								debugSet(\'open\');
								setCookie(\'debug\',\'open\');
                            }); 
                            $( document ).on(\'click\',\'.debug-smaller\',function() {
                             	debugSet(\'smaller\');
								setCookie(\'debug\',\'smaller\');
                            }); 
                            $( document ).on(\'click\',\'.debug-collapse\',function() {
								debugSet(\'collapse\');
								setCookie(\'debug\',\'collapse\');
                            });
                            $( document ).on(\'click\',\'.debug-restore\',function() {
								debugSet(\'restore\');
								setCookie(\'debug\',\'restore\');
                            });						
							
                        });
          
                    </script>';         
           
          return $html;
    }
}
?>