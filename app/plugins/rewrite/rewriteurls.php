<?php

K_Loader::load('Db/Model');

class rewriteUrlsModel extends K_Db_Model {
    var $name = 'urls';
    var $primary = 'url_url';
    
    var $allowDuplicate = false; // use on validation and save
    
    var $validate = array(
            'url_url' => array(
                    'required' => true,
                    'urlTest'
            ),
            'url_module' => array( 'regex' => '#.{1,255}#is' ),
            'url_controller' => array( 'regex' => '#.{1,255}#is' ),
            'url_action' => array( 'regex' => '#.{1,255}#is' ),
            'url_cache_key' => array( 'regex' => '#.{0,128}#is' ),
            'url_page_type' => array( 'regex' => '#.{0,100}#is' ),
            'url_status' => array( 'enum' => array('hidden','public','preview') )
    );
    
    // @TODO dopisat' function 
    public function urlTest(&$text, $fieldName ) {	
        if ( mb_strlen( $text, 'UTF-8' ) > 255 ) {
            $this->errors[ $fieldName ] = 'Максимальный размер поля 255 символов';
            return false;
        }
        if ( mb_strlen( $text, 'UTF-8' ) < 2 ) {
            $this->errors[ $fieldName ] = 'Минимальный размер поля 2 символа';
            return false;
        }
        
        if ( !$this->allowDuplicate ) {
            // test on duplicate
            $result = $this->fetchRow(
                        K_Db_Select::create()
                            ->where( array(
                                'url_url' => $text
                            ) )
                    );
            if ( count($result) ) {
                $this->errors[ $fieldName ] = 'Найден дубликат поля';
                return false;
            }
        }
        
        return true;
    }
}

?>