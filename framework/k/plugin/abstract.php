<?php

interface K_Plugin_Abstract {
	public function __construct();
	
        public function initHooks();
        
	public function setView();
}

?>
