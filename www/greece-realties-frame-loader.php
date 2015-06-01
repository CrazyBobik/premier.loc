<?


if($_SERVER['REMOTE_ADDR']=='195.138.79.81' || $_SERVER['REMOTE_ADDR']=='195.138.90.140' || $_SERVER['REMOTE_ADDR']=='127.0.0.1' || ( $ips[0] == '192' && $ips[1] == '168' )){

	ini_set('display_errors', '1');

}else{

	ini_set('display_errors', '0');

}

    function grabFilePost($loc, $postString){
	
        $ch = curl_init($loc);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
      //  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		
		if(!empty($postString)){
		
			curl_setopt($ch, CURLOPT_POST, true);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
			
		}		
		
        $data = curl_exec($ch);

        if (! $data || curl_errno($ch) != 0 || curl_getinfo($ch, CURLINFO_HTTP_CODE) > 403) {
            curl_close($ch);
            return false;
        }
		
        curl_close($ch);
        return $data;
		
    }
	
	require_once  realpath(dirname(__FILE__) . '/../configs/loader.php');
		
	$cacheManager = K_Registry::get('cacheManager');
	$cache1h =  K_Cache_Manager::get('24h');

		$args = array_merge($_POST, $_GET); 
	
		if(empty($args['page'])){
		
			$args['page']='1';
			
		}
			
		foreach($args as  $k => $v){
			
			$postElements[] = $k.'='.$v;
		
		}
	
		$sendPost = implode('&', $postElements); 
        
		$md5hash = md5($sendPost);
		  
		if (!$cache1h->test($md5hash)){//!$cache1h->test($md5hash)
			
		    $html = grabFilePost('frame-desc.moi-tour.com/Frames/RaltyObjectLeftSearchPost',  $sendPost);
			
			$html = preg_replace('/<script[.\s\S]*?\>[.\s\S]*?\<\/script\>/ims', '', $html);
						
			$html = preg_replace('/src\=\"\"/ims', 'src="'.AllConfig::$site.'/usr/plugin/greece/greece-no-foto.png"', $html);
		
			$html = preg_replace('/(href="(\/realtyobject\/\d+\?companyid=\d+)")/ims', "\$1 onclick=\"window.open('$2', 'newWin', 'Toolbar=1, Location=0, Directories=0, Status=0, Menubar=0, Scrollbars=0, Resizable=0, Copyhistory=1, Width=775, Height=755');\"", $html);
			
			$html.="<script type='text/javascript'>
						$(document).ready(function (){
							
							$('.pagination_link').click(function (){
							
								loadObjects('".preg_replace('/\&page\=\d*/is','',$sendPost)."' + '&page='+ $(this).html());
								
							});
							
						});
					</script>";
				
			K_query::query('insert into greece_pages set post_string="'.$sendPost.'", time=NOW() on duplicate key update time=NOW()');
			
			$cache1h->save($md5hash, $html);
			
		} else {
		
			$html = $cache1h->load($md5hash);
			
		}
		
		echo  $html;		
?>