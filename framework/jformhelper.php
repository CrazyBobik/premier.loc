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

class formHelper {
	
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
	
	public function select( $name, $options = array(), $value = null, $attributes = array(),$treeIds=array()) {
		$attributes['name'] = $name;
		
		if ( is_null($value) ) {
			$value = $this->_findValue( $name, null );
		}

		echo '<select '.$this->_mergeHtmlAttributes( $attributes ).'>';
                if ( count($options) ) {
                    foreach ($options as $optionKey => $optionValue) {
                        
                        if ( is_string($optionValue) ) { // without opt group
                            
                            echo '<option treeid="'.$treeIds[$optionKey].'" value="'.$optionKey.'"';
                            if ( strcmp( $value, $optionKey ) == 0 ) {
                                echo ' selected="selected"';
                            } 
                            echo '>'.$optionValue.'</option>';
                            
                        } elseif ( is_array($optionValue) ) { // with opt groups
                            
                            echo '<optgroup label="'.h( $optionKey ).'">';
                            foreach( $optionValue as $subKey => $subValue ) {
                                echo '<option value="'.$subKey.'"';
                                if ( strcmp( $value, $subKey ) == 0 ) {
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
	
	public function checkbox( $name, $value = false, $attributes = array() ) {
		$attributes['type'] = "checkbox";
		$attributes['name'] = $name;

                if ( !$value ) {
                    $value = $this->_findValue( $name, false )?true:false;
                }

		if ( $value == true ) {
			$attributes['checked'] = 'checked';
		}
		echo '<input '.$this->_mergeHtmlAttributes( $attributes ).'/>';
	}
        
        public function radio( $name, $value, $postValue = null, $attributes = array() ) {
		$attributes['type'] = "radio";
		$attributes['name'] = $name;
                $attributes['value'] = $value;
                
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
	
	protected function _mergeHtmlAttributes( &$attr, $escapeList = array() ) {
		if ( is_array($attr) ) {
			$attrArr = array();
			foreach($attr as $key => $value) {
				if ( $value != null ) {
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