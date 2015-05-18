<?php 

/**
 * Словарь
 * используется для изменения текста ошибок форм для одноязычных сайтов
 * в мультиязычных сайтах лучше использовать K_Translate
 */

class K_Dictionary {
        protected $_words = array();

	public function __construct() {}
	
	public function setWords( $wordsArray ) {
            if ( is_array($wordsArray) ) {
                $this->_words = array_merge( $this->_words , $wordsArray );
            }
        }

        public function getValue( $word ) {
            if ( array_key_exists( $word, $this->_words )  ) {
                return $this->_words[ $word ];
            }
            return $word;
        }

        public function setWord( $key, $value = '' ) {
            $this->_words[$key] = $value;
        }

        public function getWords() {
            return $this->_words;
        }

        public function loadFromIni( $path ) {
            if ( !file_exists($path) ) {
                throw('K_Dictionary->loadFromIni - file not found');
                return;
            }
            $data = parse_ini_file( $path );
            if ( count($data) ) {
                $this->setWords( $data );
            }
        }
}

?>