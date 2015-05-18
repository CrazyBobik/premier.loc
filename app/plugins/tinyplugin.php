<?php

class tinyPlugin extends K_Plugin_Base {
    var $name = 'test Plugin';
    var $version = '1.0';
    var $author = 'Denis Davydov';
    var $license = 'free';
    
    public function __construct() {
        //K_Plugins::addHook( 'view.onRenderHead', 'rewrite', 'onRender' );
    }

    public function detect( $url ) {
        return 'Global'.$url;
    }
}

?>
