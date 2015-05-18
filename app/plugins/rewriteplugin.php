<?php

class rewritePlugin extends K_Plugin_Base {
    var $name = 'ReWrite Plugin';
    var $version = '1.0';
    var $author = 'Denis Davydov';
    var $license = 'free';
    
    var $rewriteUrlsTable = null;
    
    public function __construct() {
        //K_Plugins::addHook( 'view.onRenderHead', 'rewrite', 'onRender' );
        $this->rewriteUrlsTable = new Plugin_Rewrite_RewriteUrls();
    }
    
    /**
     * Get full page info use url
     * @param String $url
     * @return Array/null
     */
    public function detect( $url ) {
        return $this->get( array( 'url_url' => $url ) );
    }
    
    /**
     * Validation test for page info record
     * @param Array $data
     * @param Boolean $allowDuplicate
     * @return true/Array(errors)
     */
    public function isValid( $data, $allowDuplicate = false ) {        
        $this->rewriteUrlsTable->allowDuplicate = $allowDuplicate;
        if ( $this->rewriteUrlsTable->isValidRow($data) ) {
            return true;
        } 
        return $this->rewriteUrlsTable->getErrors();
    }
       
    /**
     * Save page info to database and cache
     * @param Array $data
     * @param Boolean $allowDuplicate
     * @return Array(errors)/null(nothing)/int(ok - record id) 
     */
    public function save( $data, $allowDuplicate = false ) {
        // validation test fix
        $this->rewriteUrlsTable->allowDuplicate = $allowDuplicate;
        
        if ( $this->rewriteUrlsTable->isValidRow($data) ) {
            if ( isset( $data['url_params_json'] ) && is_array( $data['url_params_json'] ) ) {
                $data['url_params_json'] = json_encode( $data['url_params_json'] );
            }
            
            $this->rewriteUrlsTable->save($data);
            
            $unlimCache = K_Cache_Manager::get('unlim');
                        
            $cacheID = 'url_'.md5( trim( mb_strtolower($data['url_url'], 'utf-8') ) );
                        
            $pageID = $this->rewriteUrlsTable->lastInsertID();
            
            $unlimCache->save( $cacheID, $data );

            return $pageID;
        } else {
            return $this->rewriteUrlsTable->getErrors();
        }
        return null;
    }
    
    /**
     * Universal Get page info
     * read from cache, on error read from database & save to cache
     * @param Array $data   where array
     * @return K_Db_Row 
     */
    public function get( $data ) {
        $cacheID = null;
        if ( isset($data['url_url']) && !empty($data['url_url']) ) {
            $cacheID = 'url_'.md5( trim( mb_strtolower($data['url_url'], 'utf-8') ) );
        }
        
        if ( isset( $data['url_params_json'] ) && is_array( $data['url_params_json'] ) ) {
            $data['url_params_json'] = json_encode( $data['url_params_json'] );
        }
        
        $unlimCache = K_Cache_Manager::get('unlim');
        
        if ( !empty($cacheID) ) {            
            if ( $unlimCache->test($cacheID) ) {
                $info = $unlimCache->load($cacheID);
                if ( count($info) ) {
                    return $info;
                }
            }
        }
        
        $info = $this->rewriteUrlsTable->fetchRow( 
                K_Db_Select::create()->
                    where( $data )
                );
        
        if ( count($info) ) {
            $info = $info->toArray();
            echo $cacheID;
            //$unlimCache->save( $cacheID, $info );
            return $info;
        }
        
        return null;
    }
    
    /**
     * Remove page info
     * @param Array $data   where array
     */
    public function remove( $data ) {
        $cacheID = null;
        
        if ( isset($data['url_url']) && !empty($data['url_url']) ) {
            $cacheID = 'url_'.md5( trim( mb_strtolower($data['url_url'], 'utf-8') ) );
        }
        
        if ( isset( $data['url_params_json'] ) && is_array( $data['url_params_json'] ) ) {
            $data['url_params_json'] = json_encode( $data['url_params_json'] );
        }
        
        $unlimCache = K_Cache_Manager::get('unlim');
        
        if ( !empty($cacheID) ) {            
            if ( $unlimCache->test($cacheID) ) {
                $unlimCache->remove($cacheID);
            }
        }
        
        $this->rewriteUrlsTable->remove( 
                K_Db_Select::create()->
                    where( $data )
        );
    }
}

?>
