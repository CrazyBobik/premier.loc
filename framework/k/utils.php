<?php

class K_Utils {
        /**
         * Call an action
         * @param <type> $routerInfo
         */
	public static function callAction( $routerInfo = array() )
	{
	    $routerInfo['disableLayout'] = isset($routerInfo['disableLayout'])?$routerInfo['disableLayout']:true;
            $routerInfo['breakOnRender'] = false;
            $routerInfo['caller'] = 1;  // 0 - call from internet request, 1 - call local

            K_Capture::start();
            K_Application::get()->executeRequest( $routerInfo, true ); // execute router with autoRender attribute
            return K_Capture::end();
	}

}

?>