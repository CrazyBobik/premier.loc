<?php 

/**
 * Class Validator
 *  validators:
 * 		notEmpty
 * 		required
 * 		email
 * 		int
 * 		float
 * 		numeric
 * 		string
 */

/**
 * Error Codes:
    FORM_VALIDATE_NOT_EMPTY
    FORM_VALIDATE_REQUIRED
    FORM_VALIDATE_REGEX_FORMAT
    FORM_VALIDATE_MIN_LENGTH
    FORM_VALIDATE_MAX_LENGTH
    FORM_VALIDATE_LENGTH
    FORM_VALIDATE_ENUM_FORMAT
    FORM_VALIDATE_NOT_ENUM_FORMAT
    FORM_VALIDATE_EMAIL_FORMAT
    FORM_VALIDATE_NOT_INT
    FORM_VALIDATE_NOT_FLOAT
    FORM_VALIDATE_NOT_NUMERIC
    FORM_VALIDATE_NOT_STRING
    FORM_VALIDATE_IS_INT
    FORM_VALIDATE_IS_FLOAT
    FORM_VALIDATE_IS_NUMERIC
    FORM_VALIDATE_IS_STRING
    FORM_VALIDATE_OUT_RANGE
    FORM_VALIDATE_IN_RANGE
 */

class K_Validator implements K_Model_IValidator {
	
	var $validate = array();
	var $errors = array();
	var $fieldsNames = array();

        protected $_dictionary = null;
        protected static $_defaultDictionary = null;

        public $data = null;
	
	public function __construct( $validate = null ) {
		if ( is_array($validate) ) {
			$this->validate = $validate;
		}

                if ( !empty(self::$_defaultDictionary) ) {
                    $this->setDictionary( self::$_defaultDictionary );
                }
	}

        public static function setDefaultDictionary( &$dictionary ) {
            if ( $dictionary instanceof K_Dictionary ) {                
                self::$_defaultDictionary = &$dictionary;
            }
        }

        public function setDictionary( &$dictionary ) {
            if ( $dictionary instanceof K_Dictionary ) {
                $this->_dictionary = &$dictionary;
            }
        }
	
	public function valid( &$data = null, $validate = null ) {
		if ( !is_array($data) ) return false;
                
                $this->data = &$data;

		$usedValidate = &$this->validate;
		if ( is_array($validate) && count($validate) ) {
                    $usedValidate = &$validate;
		}

		foreach( $usedValidate as $fieldName => &$validators ) {
                    if ( !$this->_validate( $data[ $fieldName ], $validators, $fieldName ) ) {
                        return false;
                    }
		}
		return true;
	}
	
	protected function _validate( &$source, &$validators, &$fieldName ) {
            if ( is_array($validators) ) {	
                                
                foreach( $validators as $key => $value ) {
                    if ( is_int($key) && method_exists( $this, $value ) ) { // call user validation function
                        if ( !$this->$value( $source, $fieldName ) ) {
                            return false;
                        }
                    } elseif ( is_int($key) && !preg_match($value, $source) ) { // regex fast validation
                        $this->setError( $fieldName, 'FORM_VALIDATE_REGEX_FORMAT', array( $source, $value ));
                        return false;
                    } elseif ( is_string($key) && method_exists( $this, $key ) ) { // call base validators
                        if ( !$this->$key( $source, $fieldName, $value ) ) {
                            return false;
                        }
                    } else { // throw error
                        K_Debug::get()->addError('K_Validator :: Validator '.$value.' is unknown.');
                    }
                }
            }	
            return true;	
	}
        
        protected function dictionaryError( $code ) {
            return $this->_dictionary
                            ? $this->_dictionary->getValue($code) // Dictionary
                            : _($code); // Translate
        }
        
        public function setError( $fieldName, $code, $vars = array() ) {
			
            $errorString = call_user_func_array( 'sprintf', array_merge( array( $this->dictionaryError($code) ), $vars ) );
            $this->errors[ $fieldName ] = $errorString;
        }
	
	protected function notEmpty( &$text, $fieldName, $test = true ) {
            $result = !empty($text) == $test;
            if (!$result) {
                $this->setError( $fieldName, 'FORM_VALIDATE_NOT_EMPTY', array( $test ) );
            }
            return $result;
	}
	
	protected function required( &$text, $fieldName, $test = true ) {
            $result = ($test && isset($text) && !empty($text)) || !$test;
            if (!$result) {
                $this->setError( $fieldName, 'FORM_VALIDATE_REQUIRED', array($test ) );
            }
            return $result;
	}

        /**
         * Function regex - check value on regural expression
         * @param string $text              field value
         * @param string $fieldName         field name
         * @param string|array $regex       string(regex pattern) or array['pattern',|'code'|]
         * @return bool
         * <example>
         *  'regex' => [
         *      'pattern' => '/^[a-z]+$/is',
         *      'code' => 'Плохая ошипко'
         *  ],
         *  'regex' => '/[0-9]/is'
         * </example>
         */
        protected function regex( &$text, $fieldName, $regex = '' ) {
            
            $regexString = $regex;
            $code = 'FORM_VALIDATE_REGEX_FORMAT';
            
            if ( is_array($regex) && isset( $regex['pattern'] ) ) {
                $regexString = $regex['pattern'];
                if ( isset($regex['code']) ) {
                    $code = $regex['code'];
                }
            }
            
            $result = preg_match( $regexString, $text ) > 0;

            if (!$result) {
                $this->setError( $fieldName, $code,array( $text, $regexString ) );
            }
            return $result;
        }
        
        protected function minlen( &$text, $fieldName, $minlen = 0 ) {
            $result = mb_strlen($text, 'UTF-8') >= $minlen;

            if (!$result) {
                $this->setError( $fieldName, 'FORM_VALIDATE_MIN_LENGTH', array( $minlen ) );
            }
            return $result;
        }
        
        protected function maxlen( &$text, $fieldName, $maxlen = 0 ) {
            $result = mb_strlen($text, 'UTF-8') <= $maxlen;

            if (!$result) {
                $this->setError( $fieldName, 'FORM_VALIDATE_MAX_LENGTH', array( $maxlen ) );
            }
            return $result;
        }
        
        protected function length( &$text, $fieldName, $params =  array() ) {
            
            $min = isset($params, $params['min']) ? (int)$params['min'] : 0;
            $max = isset($params, $params['max']) && (int)$params['max'] >= $min ? (int)$params['max'] : 0;
            
            if ( mb_strlen($text, 'UTF-8') < $min ) {
                $this->setError( $fieldName, 'FORM_VALIDATE_LENGTH', array( $min, $max ) );
                return false;
            }
            
            if ( mb_strlen($text, 'UTF-8') > $max ) {
                $this->setError( $fieldName, 'FORM_VALIDATE_LENGTH', array( $min, $max ) );
                return false;
            }
            
            return true;
        }
        
        protected function range( &$text, $fieldName, $params =  array() ) {
            if ( isset($params[0], $params[1]) && is_numeric($params[0]) && is_numeric($params[1]) ) {
                $result = $text >= $params[0] && $text <= $params[1];
                if ( !$result ) {
                    $this->setError( $fieldName, 'FORM_VALIDATE_OUT_RANGE', array( $params[0], $params[1] ) );
                    return false;
                }
                return true;
            }            
            K_Debug::get()->addError( 'K_Validator :: range - undefined range');
            return true;
        }
        
        protected function outRange( &$text, $fieldName, $params = array() ) {            
            if ( isset($params[0], $params[1]) && is_numeric($params[0]) && is_numeric($params[1]) ) {
                $result = $text >= $params[0] && $text <= $params[1];
                if ( $result ) {
                    $this->setError( $fieldName, 'FORM_VALIDATE_IN_RANGE', array( $params[0], $params[1] ) );
                    return false;
                }
                return true;
            }            
            K_Debug::get()->addError( 'K_Validator :: outRange - undefined range');
            return true;
        }
        
        protected function enum( &$text, $fieldName, $variants = array() ) {
            if (in_array( $text, $variants ) ) {
                return true;
            }
            
            $this->setError( $fieldName, 'FORM_VALIDATE_ENUM_FORMAT', array( $text ) );
            return false;
        }
        
        protected function notEnum( &$text, $fieldName, $variants = array() ) {
            if (!in_array( $text, $variants ) ) {
                return true;
            }
            
            $this->setError( $fieldName, 'FORM_VALIDATE_NOT_ENUM_FORMAT', array( $text ) );
            return false;
        }
	
	protected function email( &$text, $fieldName ) {
	   
            $result = preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $text ) > 0;
            if (!$result) {
                $this->setError( $fieldName, 'FORM_VALIDATE_EMAIL_FORMAT', array( $text ) );
            }
            return $result;
            
	}
	
	protected function int( &$text, $fieldName, $test = true ) {
            $result = is_int( $text ) || (string)((int)$text) == trim($text);
            
            $result = $result == $test;
            
            if (!$result) {
                if ( $test ) {
                    $this->setError( $fieldName, 'FORM_VALIDATE_NOT_INT', array( $text ) );
                } else {
                    $this->setError( $fieldName, 'FORM_VALIDATE_IS_INT', array( $text ) );
                }
            }
            
            return $result;
	}
	
	protected function float( &$text, $fieldName, $test = true ) {
	   
          if(strpos($text,',')){
           $text = str_replace(',', '.', $text);
          }
     
          $result = floatval($text);
                 
          $result = $result == $test;
            
            if (!$result) {
                if ( $test ) {
                    $this->setError( $fieldName, 'FORM_VALIDATE_NOT_FLOAT', array( $text ) );
                } else {
                    $this->setError( $fieldName, 'FORM_VALIDATE_IS_FLOAT', array( $text ) );
                }
            }
            return $result;
	}
	
	protected function numeric( &$text, $fieldName, $test = true ) {
	        
          if(strpos($text,',')){
           $text = str_replace(',', '.', $text);
          }
       
       
            $result = is_numeric($text) || is_int($text) || $this->float( $text, $fieldName);
            
            
            $result = $result == $test;
            
            if (!$result) {
                if ( $test ) {
                    $this->setError( $fieldName, 'FORM_VALIDATE_NOT_NUMERIC', array($text ) );
                } else {
                    $this->setError( $fieldName, 'FORM_VALIDATE_IS_NUMERIC', array( $text ) );
                }
            }
            
            return $result;
	}
	
	protected function string( &$text, $fieldName, $test = true ) {
            $result = is_string( $text );
            
            $result = $result == $test;
            
            if (!$result) {
                if ( $test ) {
                    $this->setError( $fieldName, 'FORM_VALIDATE_NOT_STRING',  array( $text ) );
                } else {
                    $this->setError( $fieldName, 'FORM_VALIDATE_IS_STRING',  array( $text ) );
                }
            }
            return $result;
	}
	
    
    protected function alphanumeric(&$text, $fieldName) {
        if (preg_match('/[^а-яa-z0-9_]+/ui', $text)) {
            $this->errors[$fieldName] = 'для этого поля разрешены только буквы и цифры';
            return false;
        }
        return true;
    }

    protected function ealphanumeric(&$text, $fieldName) {
        if (preg_match('/[^a-z0-9_]/i', $text)) {
            $this->errors[$fieldName] = 'для этого поля разрешены только латинские буквы и цифры';
            return false;
        }
        return true;
    }
    
  	public function getError( $fieldName ) {
		return isset( $this->errors[ $fieldName ] ) ? $this->errors[ $fieldName ] : '';
	}
	
	public function getErrors() {
	    return $this->errors;
	}
    
    /**
    * Метод сразу переводит ключи валидатора в названия полей по словарю
    * @param $fieldsNames['user_name']='Имя пользователя'
    * @rerun $errors = array(['user_name']=>array('label'=>'Имя пользователя','error'=>'Ошибка вылидации'))
    */ 
    public function getErrorsD($fieldsNames){
          
		// если не установлен словарь, берём словарь по умолчанию   
		if(!isset($fieldsNames) || empty($fieldsNames)){
			
		    $fieldsNames=$this->fieldsNames;
		
		}
		   //vd1($this->errors);
        foreach($this->errors as $k=>$v){   
         
			$errorArr['label']=$fieldsNames[$k];
			$errorArr['error']=$v;
			$errors[$k]=$errorArr;
		   
        }
		//vd1($this->errors);	 
 	    return $errors;
	}
}

?>