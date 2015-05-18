<?php 

/**
 * Class K_TemplateRender
 *
 * Render for template
 * use in emails
 *
 * <example>
 *
 * $tags = array(
 *  'mail' => 'mistersmith@mail.ru',
 *  'message' => 'how are you?'
 * );
 *
 * $text = '<html><body>From: {mail}<br/>Message: {message}</body></html>';
 *
 * $render = new K_TemplateRender();
 *
 * echo $render->assemble( $text );
 * // result -> <html><body>From: mistersmith@mail.ru<br/>Message: how are you?</body></html>
 *
 * // set new tags for replace
 * $render->setBorders( '@', '' );
 *
 * $render->fromString( 'mail: @mail, message: @message' );
 * echo $render->assemble();
 * // result -> mail: mistersmith@mail.ru, message: how are you?
 *
 * </example>
 */

class K_TemplateRender {
        protected $_text = null;
        protected $_tags = null;
        protected $_leftBorder = '{';
        protected $_rightBorder = '}';

	public function __construct( $string = null, $tags = null, $left = '{', $right = '}' ) {
            if ( !empty($string) && is_string($string) ) {
                $this->_text = $string;
            }
            if ( !empty($tags) && is_array($tags) ) {
                $this->_tags = $tags;
            }
            if ( !empty($left) ) {
                $this->_leftBorder = $left;
            }
            if ( !empty($right) ) {
                $this->_rightBorder = $right;
            }
        }

    	public function fromFile( $path ) {
            if (file_exists($path)) {
                $this->fromString( file_get_contents( $path ) );
            } else {
                throw( 'K_TemplateRender :: template file '.$path.' not found in path' );
            }
        }

        public function fromString( $string ) {
            $this->_text = $string;
        }

        public function setBorders( $left, $right ) {
            $this->_leftBorder = $left;
            $this->_rightBorder = $right;
        }

        public function setTags( $tags ) {
            if ( is_array($tags) ) {
                $this->_tags = $tags;
            }
        }

        public function assemble( $text = null ) {
            $searchList = array();
            if ( is_array($this->_tags) && count($this->_tags) ) {
                foreach ( $this->_tags as $tag => &$source ) {
                    $searchList[] = $this->_leftBorder.$tag.$this->_rightBorder;
                }
            }
            $textForReplace;
            if ( !empty($text) ) {
                $textForReplace = $text;
            } else {
                $textForReplace = $this->_text;
            }
            return str_replace( $searchList, array_values($this->_tags), $textForReplace );
        }
        
        public static function rendFile ( $file, $tags, $left = '<%', $right = '%>' ){
         
               $render = new K_TemplateRender(null,  null, $left , $right );
      
               $render->setTags($tags);
                        
               $render->fromFile($file);
                    
               return $render->assemble();
       
        }
        
        public static function rendString ( $string, $tags, $left = '<%', $right = '%>' ){
         
               $render = new K_TemplateRender(null,  null, $left , $right );
      
               $render->setTags($tags);
                        
               $render->fromString($string);
                    
               return $render->assemble();
       
        }
        
        
}

?>