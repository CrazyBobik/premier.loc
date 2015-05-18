<?php 

/**
 * Class K_User
 */

define ('DMA_CT_GUEST', 'guest');
define ('DMA_CT_USER',  'user');
define ('DMA_CT_ADMIN', 'admin');

class K_User{
    
	protected static $_userInfo = null;

	protected static $_options = null;
	
	protected static $_isInit = false;

    protected static function init() {
    	if ( self::$_isInit ) return;
    	self::$_userInfo = K_Session::read('UserInfo', 'K_User');
    	self::$_options = K_Session::read('Options', 'K_User');
    	if ( empty(self::$_options) ) {
    		self::$_options = array();
    		self::$_options['isLogin'] = false;
           	self::$_options['isAdmin'] = false;
    		self::$_options['accessLevel'] = 0;
    		self::$_options['clientType'] = DMA_CT_GUEST;
            self::$_options['roles'] = array('guests');
    	}
    	self::$_isInit = true;
	}

   	public static function singin($userInfo, $clientType = DMA_CT_GUEST, $isAdmin = false, $accessLevel = 0 ) {
        self::init();
		self::$_userInfo = $userInfo;
		K_Session::write('UserInfo', self::$_userInfo, 'K_User');
    	self::$_options = array();
		self::$_options['isUserLogin'] = true;
		self::$_options['isAdmin'] = $isAdmin;
		self::$_options['accessLevel'] = $accessLevel;
		self::$_options['clientType'] = $clientType;
        self::$_options['roles'] = array('guests');
  		K_Session::write('Options', self::$_options, 'K_User');
        // Кеш для релогин триггера, если он есть то пользователя пускаем, если кеша нет то вылогиневаем;   
        $data=true;
	    K_Cache_Manager::get('24h')->save('RL'.$userInfo['id'],$data);
 	}
	
	public static function logout() {
        self::init();
        // Кеш для релогин триггера, удаляем кеш, так как он уже не нужен
        K_Cache_Manager::get('24h')->remove('RL'.self::$_userInfo['id']);
        
		self::$_userInfo = null;
		K_Session::remove('UserInfo', 'K_User');
		
		self::$_options = array();
		self::$_options['isLogin'] = false;
		self::$_options['isUserlogout'] = false;
		self::$_options['isAdmin'] = false;
		self::$_options['accessLevel'] = 0;
		self::$_options['clientType'] = DMA_CT_GUEST;
        self::$_options['roles'] =array('guests');        
		K_Session::write('Options', self::$_options, 'K_User');
        
	}
	
	public static function isAdmin() {
        self::init();
		if ( isset(self::$_options, self::$_options['isAdmin']) ) {
			return self::$_options['isAdmin'];
		}
		return false;
	}
  
	public static function isLogin() {
        self::init();
		if ( isset(self::$_options, self::$_options['isLogin']) ) {
			return self::$_options['isLogin'];
		}
		return false;
	}
   	
	public static function getInfo( $key = null ) {
        self::init();
		if ( isset(self::$_userInfo) && !empty(self::$_userInfo) ) {
			if ( $key == null ) {
				return self::$_userInfo;
			} else {
				if ( isset(self::$_userInfo[ $key ]) ) {
					return self::$_userInfo[ $key ];
				}
			}
		}
		return null;
	}

	public static function clientType() {
        self::init();
		if ( isset(self::$_options, self::$_options['clientType']) ) {
			return self::$_options['clientType'];
		}
		return DMA_CT_GUEST;
	}
       
    public static function setUserInfo( $data ) {
        if ( is_array($data) ) {
            self::$_userInfo = $data;
            K_Session::write('UserInfo', self::$_userInfo, 'K_User');
        }
    }
        
    public static function mergeUserInfo( $data ) {
        if ( is_array($data) ) {
            
            $baseData = self::getUserInfo();
            self::setUserInfo( array_merge( $baseData, $data ) );
        }
    }
        
    public static function setUserKey( $key, $value ) {
        if ( !is_array(self::$_userInfo) ) {
            self::$_userInfo = array();
        }
        self::$_userInfo[ $key ] = $value;
        K_Session::write('UserInfo', self::$_userInfo, 'K_User');
    }
}

?>