<?php

class K_Form {
	protected $data;
	protected $files;

        static $imageFiles = array( "image/jpeg", "image/gif", "image/png" );
        static $excelFiles = array( "application/msexcel", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/x-msexcel", "application/x-ms-excel", "application/x-excel", "application/x-dos_ms_excel", "application/xls", "application/x-xls" );

	public function __construct() {
            if ( is_array($_POST) ) {
                $this->data = $_POST;
            } else {
                $this->data = array();
            }

            if ( is_array($_FILES) ) {
                $this->files = $_FILES;
            } else {
                $this->files = array();
            }
	}

        public function getData() {
            return $this->data;
        }

        public function setElement( $name, $value ) {
            $this->data[ $name ] = $value;
        }

        public function getElement( $name ) {
            if ( isset($this->data[ $name ])) {
                return $this->data[ $name ];
            }
            return null;
        }

        public function hasFiles() {
            return count( $this->files ) > 0;
        }

        public function allowType( $name, $type ) {
            if ( is_string($type) ) {
                return  strtolower( trim($this->files[ $name ][ 'type' ]) ) == strtolower( trim($type) );
            } elseif ( is_array($type) ) {
                foreach( $type as &$typeVariant ) {
                    if ( strtolower( trim($this->files[ $name ][ 'type' ]) ) == strtolower( trim($typeVariant) ) ) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function getFiles() {
            return $this->files;
        }
    
        public function securityFileName( $name ) {
            $sname = $name;
            return $sname;
        }

/** moveUploadedFile - перемещение загруженного файла
 *  
 * @return array('path' => $path, 'filename' => $fileName)
 * @todo Добавить функционала ( генерацию названия, проверка на наявность такого файла, разные префиксы и суфФиксы)
 */

        public function moveUploadedFile( $name, $uploadDir, $newName = null, $genFileName = false) {
            if ( !is_dir($uploadDir) ) {
                throw new Exception( 'Upload dir is not exists. '.$uploadDir );
            }
            if ( isset($this->files[$name]) ) {
                if ( $this->files[$name]['error'] == UPLOAD_ERR_OK ) {
                    $tmp_name = $_FILES[ $name ]["tmp_name"];
                    $fileName = '';
                    if ( !empty($newName) ) {
                        $fileName = $newName;
                    } else {
                        $fileName = $this->files[ $name ]["name"];
                    }
                    
                    $matches = null;
                    $ext = '';
                    
                    if ( preg_match('/.*(\.(.*?))$/is', $this->files[ $name ]["name"], $matches) ) {
                        $ext = $matches[2];
                    }
                    
					if ($genFileName)
					{
						$fileName = md5( time() . $fileName ).'.'.$ext;
						while ( file_exists(realpath($uploadDir).'/'.$fileName) ) {
							$fileName = md5( time() . $fileName ).'.'.$ext;
						}
					}
					else
					{
						$fileName .= '.'.$ext;
					}
                    
                    $path =  realpath($uploadDir).'/'.$fileName;
                    
                    unlink($path);
                    
                    move_uploaded_file($tmp_name, $path);
                    
                    return array( 'path' => $path, 'filename' => $fileName);
                }
            } else {
                
                return null;
                
            }
        }
        
/**
 * moveUploadedFile - перемещение всех загруженных файлов 
 * 
 * @param $allowType 
 * 
 * @return $result
 */        
   
        public function moveAllUploadedFiles( $uploadDir, $allowType = null ) {
            
            $result = array();
            
            if ( $this->hasFiles() ) {
                $formFields = array_keys( $this->files );
                foreach( $formFields as &$fieldName ) {
                    if ( empty($allowType) || (!empty($allowType) && $this->allowType( $fieldName, $allowType) ) ) {
                        $result[ $fieldName ] = $this->moveUploadedFile( $fieldName, $uploadDir );
                    }
                }
            }
            
            return $result;
        }
}