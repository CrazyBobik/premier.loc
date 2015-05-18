<?php 

function vd1($dumpVar){

	if($_SERVER['REMOTE_ADDR']=='37.53.56.80' ||$_SERVER['REMOTE_ADDR']=='195.138.90.140' || $_SERVER['REMOTE_ADDR']=='195.138.79.81' || $_SERVER['REMOTE_ADDR']=='127.0.0.1' ){
	
		var_dump($dumpVar);
	
	}
	
}  

function phs( $text, $nl2br = false ) {
    return $nl2br ? nl2br( htmlspecialchars( $text ) ) : htmlspecialchars( $text );
}

function ephs( $text, $nl2br = false ) {
    echo $nl2br ? nl2br( htmlspecialchars( $text ) ) : htmlspecialchars( $text );
}
	
function h( $text ) {
    return htmlspecialchars( $text );
}

function eh( $text ) {
    echo htmlspecialchars( $text );
}

function t($def, $en){
    
    if(Allconfig::$contentLang == Allconfig::$defoultLang){
             
       return $def;
		
    }else{
    
       return  $en;
       
    }
}

function linkL($razdel, $fullLink ){

	$razdel =trim($razdel,'/');
	
    if(Allconfig::$contentLang == Allconfig::$defoultLang){
		
        return rtrim(str_replace('/'.$razdel.'/'.Allconfig::$contentLang, '', $fullLink),'/');// удаляем язык
       
    }else{
   
		return rtrim(str_replace('/'.$razdel, '', $fullLink),'/'); // оставляем язык
       
    }

}

function linkRemoveLang($link, $langList){

	$linkArr = explode('/' , $link);
	
	if( in_array($linkArr[1], $langList)){
				
		unset($linkArr[1]);
		
	}
	
	return trim(implode('/', $linkArr),'/	');
	
}


function r($data, $field ){
     
    if(Allconfig::$contentLang == 'en'){
        
       return  $data[$field.'_en'];
       
    }else{
    
       return  $data[$field];
    }
}

function l(){
   // var_dump(Allconfig::$contentLang);
    if(Allconfig::$contentLang == 'ru'){
   
		return ''; 
        
    }else{
    
		return '/'.Allconfig::$contentLang.'';
       
    }
}

function href($link){
    
   $link = trim($link);
	
   return  l().($link=='/'? $link: rtrim( $link, '/') );
   
}


/** @function select - ni?eau?iiue aa?eaio auciaa eiino?oeoi?a K_Db_Select
 * 
 */

function select($fields = null){
    
    return K_Db_Select::create($fields);
    
}

?>