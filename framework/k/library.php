<?php 

class K_Library {
	/**
     * Search in array
     * <example>
     *  K_ClassGenerator::arrayPathSearch( $myArr, 'main/result/item/name' );
     * </example>
     * @param refArray $array   array for search
     * @param string $path      search path
     * @param bool $asString    return as string?
     * return array/string
     */
    public static function arrayPathSearch( &$array, $path, $asString = false ) {
        if ( !(is_string($path) && strlen(trim($path))) ) {
            return false;
        }
        
        $pathParts = explode('/', $path);
        
        if ( count($pathParts) == 0 ) {
            return false;
        } 
        
        $arr = &$array;
        foreach( $pathParts as $part ) {
            if ( $part == '*' || $part=='' ) {
                break;
            }
            if ( isset($arr[ $part ]) ) {
                $arr = &$arr[ $part ];
            } else {
                return null;
            }
        }
        return $asString ? K_Library::buildVariableValue($arr) : $arr;
    }
    
    // My equivalent of var_export - improve
    public static function buildVariableValue( &$data = [] ) {
        if ( is_array($data) ) {
            if ( count($data) == 0 ) {
                return '[]';
            } else {
                $results = '';
                foreach( $data as $key => &$dataElement ) {
                    $results .=  K_Library::buildVariableValue($key).'=>'.K_Library::buildVariableValue( $dataElement );
                    if ( end($data) !== $dataElement ) {
                        $results .= ',';
                    }
                }
                return '['.$results.']';
            }
        } else {
            if ( is_string($data) ) {
                return '\''.addcslashes( $data, '\'').'\'';
            } elseif (is_numeric( $data) ) {
                return (string)$data;
            } elseif (is_bool($data) ) {
                return $data? 'true':'false';
            } elseif (is_null($data) || empty($data) ) {
                return 'null';
            } else {
                return var_export($data);
            }
        }
    }
    
}

?>