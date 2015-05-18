<?php 

/**
 * Form Helper
 * <example> // in template
 	<?php $this->html->link( $linkAttr ); ?>
 * </example>
 */

define('K_URL_KEYTYPE_CLASSIC', '&');
define('K_URL_KEYTYPE_SLASHED', '/');

class htmlHelper {
	
	public function link( $attributes = array() ) {
		$html = '';
		if ( isset($attributes['html']) ) {
			$html = $attributes['html'];
			unset($attributes['html']);
		}		
		echo '<a '.$this->_mergeHtmlAttributes( $attributes ).'>'.$html.'</a>';
	}

        /**
         * Build url use path+data attributes key=value&... or /key/value...
         * @param <String> $data
         * @param <String> $replace
         * @param <String> $keyType
         */
        public function url( $path = '', $data = array(), $replace = array(), $keyType = K_URL_KEYTYPE_SLASHED ) {
            $url = rtrim($path,"/");
            if ( is_array($data) && is_array($replace) ) {
                $data = array_merge( $data, $replace );
            }
            switch ($keyType) {
                case K_URL_KEYTYPE_SLASHED:
                    if ( count($data) ) {
                        foreach( $data as $key => &$value ) {
                            $url .= '/'.urldecode($key).'/'.urldecode($value);
                        }
                    }
                    break;
                case K_URL_KEYTYPE_CLASSIC:
                default:
                    $url .= '?'.http_build_query($data);
                    break;
            }
            return $url;
        }
	
	protected function _mergeHtmlAttributes( &$attr, $escapeList = array() ) {
		if ( is_array($attr) ) {
			$attrArr = array();
			foreach($attr as $key => $value) {
				if ( $value != null ) {
					if ( in_array( $value, $escapeList ) ) {
						$attrArr[] = $key.'="'.escape( $value ).'"';
					} else {
						$attrArr[] = $key.'="'.addcslashes( $value , '"' ).'"';
					}
				}
			}
			return implode(' ', $attrArr);
		}
		return '';
	}
}

?>