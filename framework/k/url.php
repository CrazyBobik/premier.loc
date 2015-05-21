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

class K_Url {

    protected static $instance = null;
    public $urlRaw = null;
    public $urlParsed = null;
    public $expPath = array();

    protected function __construct($url){

        $this->urlRaw = $url;

        $this->urlParsed = parse_url($url);

        $this->expPath = explode('/', trim($this->urlParsed['path'], '/'));

    }

    public static function URLLangLink($link){

        $link = trim($link, '/');

        $arr = explode('/', $link);
        foreach ($arr as $v){
            if ($v != AllConfig::$contentLang){
                $array[] = $v;
            }
        }

        return implode('/', $array)."/";
    }

    // Get K_Url saved instance for this url
    public static function get() {

        if( !self::$instance ){

            self::$instance = new K_Url($_SERVER['REQUEST_URI']);

            return self::$instance;

        }

        return self::$instance;
    }

    public function getParams() {

        return $this->urlParsed['query'];

    }

    // Get K_Url saved instance for this url
    public function getPath() {

        return $this->urlParsed['path'];

    }

    public function getExpPath() {

        return $this->expPath;

    }

    // Get K_Url instance
    public function loadUrl() {


    }

    //Удаляет один урл из другого

}

?>