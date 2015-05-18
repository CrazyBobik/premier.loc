<?php 

class K_Locale {
	protected static $locale = 'ru_RU';
	
	protected function __construct() {}
		
	public static function set( $locale ) {
		putenv('LC_ALL='.$locale);
                //putenv('LC_LANG='.$locale);
                //putenv('LC_LANGUAGE='.$locale);
		setlocale(LC_ALL, $locale);
		self::$locale = $locale;
	}
	
	public static function getLocale() {
		return self::$locale;
	}
}

?>