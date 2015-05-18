<?php 

function t($rus, $ukr){
    
    if(K_Registry::get('lang')=='uk'){
        
       return $rus;
       
    }else{
    
       return $ukr;
       
    }
}

function l(){
    
    if(K_Registry::get('lang')=='uk'){
        
       return '/uk';
       
    }else{
    
       return '';
       
    }
}

?>