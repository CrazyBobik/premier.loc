<?php 

class K_Config {
	public static function load( $path ) {
		if ( !is_file($path) ) return FALSE;
		$data = array();
		$iniData = parse_ini_file( $path, true );
		if ( is_array($iniData) && count($iniData) ) {
			self::_circle($iniData, $data);
		}
		unset($iniData);
		return $data;
	}
	
	protected static function _circle( &$iniData, &$data ) {
		foreach( $iniData as $key => &$value ) {
			if ( is_string($value) ) {
				$key = explode('.', $key);
				$v = &$data;
				if ( is_array($key) && count($key) ) {					
					foreach($key as &$subKey) {
						$v = &$v[ $subKey ];
					}
				} else {
					$v = &$v[ $key ];
				}
				$v = $value;
			} elseif ( is_array($value) ) {
				self::_circle( $iniData[$key], $data[$key] );
			}
		}
	}
}
	