<?php
/*
    CLASS K_BINDING_MODEL




*/

namespace K_Model_BindingModel {
    
    class K_BindingModel {
        
        protected $data;
        protected $model;
        
        public function __construct() {
	
	}
        
        public function fromFile( $fileName ) {
            if ( is_file($fileName) ) {
                $this->fromString( file_get_contents( $fileName ) );
            } else {
                throw new Exception('K_BindingModel->fromFile: model file not found ('. $fileName .')' );
            }
        }
        
        public function fromString( $string ) {
            $this->data = json_decode( $str, true );
        }
        
        public function buildModel() {
            
        }

    }

}