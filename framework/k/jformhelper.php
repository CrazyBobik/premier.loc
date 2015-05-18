<?php 

/**
 * Form Helper
 * <example> // in template
 	<?php $this->form->begin(); ?>
	<?php $this->form->text( 'core_page_id' ); ?>
	<?php $this->form->select( 'selecttest', array('123'=>'123', '345'=>'345'), '', array() ); ?>
	<?php $this->form->checkbox( 'ch1', true ); ?>
	<?php $this->form->textarea( 'ta1', null, array('cols'=>80, 'rows'=>40) ); ?>
	<?php $this->form->submit(); ?>
	<?php $this->form->end(); ?>
 * </example>
 */

class K_jFormHelper {
	
	public function begin( $action = '', $method="post", $attributes = array() ) {
		$attrList = array_merge( $attributes, array( 'method'=>$method, 'action'=>$action ) );
		$attrString = $this->_mergeHtmlAttributes( $attrList, array('action') );
		echo '<form '.$attrString.'>';
	}
	
	public function end() {
		echo '</form>';
	}
	
	public function text( $name, $value = null, $attributes = array() ) {
		$attributes['type'] = "text";
		$attributes['name'] = $name;
		if ( is_string($value) ) {
			$attributes['value'] = $value;
		} else {
			$attributes['value'] = $this->_findValue( $name, null );
		}
		echo '<input '.$this->_mergeHtmlAttributes( $attributes, array('value') ).'/>';
	}
	
	public function hidden( $name, $value = null, $attributes = array() ) {
		$attributes['type'] = "hidden";
		$attributes['name'] = $name;
		if ( is_string($value) ) {
			$attributes['value'] = $value;
		} else {
			$attributes['value'] = $this->_findValue( $name, null );
		}
		echo '<input '.$this->_mergeHtmlAttributes( $attributes, array('value') ).'/>';
	}
	
	public function password( $name, $value = null, $attributes = array() ) {
		$attributes['type'] = "password";	
		$attributes['name'] = $name;
		if ( is_string($value) ) {
			$attributes['value'] = $value;
		} else {
			$attributes['value'] = $this->_findValue( $name, null );
		}
		echo '<input '.$this->_mergeHtmlAttributes( $attributes, array('value') ).'/>';
	}

	public function file( $name, $attributes = array() ) {
		$attributes['type'] = "file";
		$attributes['name'] = $name;
		echo '<input '.$this->_mergeHtmlAttributes( $attributes ).'/>';
	}
	
	public function submit( $value = null, $attributes = array() ) {
		$attributes['type'] = "submit";	
		if ( is_string($value) ) {
			$attributes['value'] = $value;
		} 
		echo '<input '.$this->_mergeHtmlAttributes( $attributes, array('value') ).'/>';
	}
	
	public function reset( $value = null, $attributes = array() ) {
		$attributes['type'] = "reset";	
		if ( is_string($value) ) {
			$attributes['value'] = $value;
		} 
		echo '<input '.$this->_mergeHtmlAttributes( $attributes ).'/>';
	}
	
	public function select( $name, $options = array(), $value = null, $attributes = array(), $treeIds=array()) {
		$attributes['name'] = $name;
		         
		if ( is_null($value) ) {
			$value = $this->_findValue( $name, null );
		}

		echo '<select '.$this->_mergeHtmlAttributes( $attributes ).'>';
                if ( count($options) ) {
                    foreach ($options as $optionKey => $optionValue) {
                        
                        if ( is_string($optionValue) ) { // without opt group
                            
                            echo '<option treeid="'.$treeIds[$optionKey].'" value="'.$optionKey.'"';
                          
                            if ($value==$optionKey ) {
                                echo ' selected="selected"';
                            } 
                            echo '>'.$optionValue.'</option>';
                            
                        } elseif ( is_array($optionValue) ) { // with opt groups
                            
                            echo '<optgroup label="'.h( $optionKey ).'">';
                            foreach( $optionValue as $subKey => $subValue ) {
                                echo '<option value="'.$subKey.'"';
                                if ($value==$subKey ) {
                                    echo ' selected="selected"';
                                } 
                                echo '>'.$subValue.'</option>';
                            }
                            echo '</optgroup>';
                            
                        }
                        
                            
                    }
                }
		echo '</select>';
	}
	
	public function checkbox( $name, $value = false, $postValue = null, $attributes = array() ) {
	   
		$attributes['type'] = "checkbox";
		$attributes['name'] = $name;
        $attributes['value'] = $postValue;
           
           if ( is_null($postValue) ) {
                    $test = $this->_findValue( $name, false ) === $value ? true : false;
                    if ( $test ) {
                        $attributes['checked'] = 'checked';
                    }
                }       
                
           echo '<input '.$this->_mergeHtmlAttributes( $attributes ).'/>';
	}
        
    public function radio( $name, $value  = false, $postValue = null, $attributes = array() ) {
	
       	$attributes['type'] = "radio";
		$attributes['name'] = $name;
        $attributes['value'] = $postValue;
                
                if ( is_null($postValue) ) {
                    $test = $this->_findValue( $name, false ) === $value ? true : false;
                    if ( $test ) {
                        $attributes['checked'] = 'checked';
                    }
                }

       

		echo '<input '.$this->_mergeHtmlAttributes( $attributes ).'/>';
	}
	
	public function textarea( $name, $value = '', $attributes = array() ) {
		$attributes['name'] = $name;
		if ( empty($value) ) {
			$value = $this->_findValue( $name, null );
		} 
		echo '<textarea '.$this->_mergeHtmlAttributes( $attributes ).'>'.htmlspecialchars($value).'</textarea>';
	}
    
   	public function options(  $options = array(), $selectedValue ) {
   	    
  		 $optionsHtml = '';
        
   	     if ( count($options) ) {
   	        
                    foreach ($options as $optionKey => $optionAtrrs) {
                       
                        if ( $optionAtrrs ) { 
                            
                            if (isset($selectedValue) && $optionAtrrs['value']== $selectedValue) {
                                $optionAtrrs['selected']='selected';
                            } 
                            
                            $optionsHtml.= '<option '.self::genAttrs($optionAtrrs).'';
                            $optionsHtml.= '>'.$optionKey.'</option>';
                            
                       } elseif ( is_array($optionAtrrs[0]) ) { // with opt groups
                            
                            $optionsHtml.= '<optgroup label="'.h( $optionKey ).'">';
                            
                            foreach( $optionValue as $subKey => $subAtrrs ) {
                                
                                    if ($subAtrrs['value']== $selectedValue) {
                                        $subAtrrs['selected']='selected';
                                    } 
                                    
                                    $optionsHtml.= '<option '.self::genAttrs($subAtrrs).'';
                                    $optionsHtml.= '>'.$optionKey.'</option>';
                                    
                            }
                            $optionsHtml.= '</optgroup>';
                      }
                 }
         }
         
         return $optionsHtml;
         
  	}
	
	protected function _mergeHtmlAttributes( &$attr, $escapeList = array() ) {
		if ( is_array($attr) ) {
			$attrArr = array();
			foreach($attr as $key => $value) {
				if (!is_null($value)) {
					if ( in_array( $key, $escapeList ) ) {
						$attrArr[] = $key.'="'.htmlspecialchars( $value ).'"';
					} else {
						$attrArr[] = $key.'="'.addcslashes( $value , '"' ).'"';
					}
				}
			}
			return implode(' ', $attrArr);
		}
		return '';
	}
        
   	public static function genAttrs( &$attr, $escapeList = array() ) {
   	    
		if ( is_array($attr) ) {
			$attrArr = array();
			foreach($attr as $key => $value) {
				if ( !is_null($value) ) {
					if ( in_array( $key, $escapeList ) ) {
						$attrArr[] = $key.'="'.htmlspecialchars( $value ).'"';
					} else {
						$attrArr[] = $key.'="'.addcslashes( $value , '"' ).'"';
					}
				}
			}
			return implode(' ', $attrArr);
		}
        
		return '';
        
	}    
	
	protected function _findValue( $name, $defaultValue = null ) {
                if ( isset($_POST[ $name ]) ) {
			return $_POST[ $name ];
		} elseif (isset($_GET[ $name ])) {
			return $_GET[ $name ];
		}
		return $defaultValue;
	}
}

?>