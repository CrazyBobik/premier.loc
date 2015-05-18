<?php 

/**
 * ERROR HELPER
 * 
 * $_SESSION['FlashError'] => message
 * $_SESSION['IgnoreFlashError'] => !empty => show message on next page
 */

class errorHelper {

	protected $ignore = false;

	public function __construct() {
		if ( K_Session::test('Global.IgnoreFlashError') ) {
			$this->ignore = true;
			K_Session::remove('Global.IgnoreFlashError');		
		}
	}
	
	public function check( $paramName, &$errorsList ) {
		if ( isset($errorsList[ $paramName ]) && !empty( $errorsList[ $paramName ] ) ) {
			echo '<div class="form-error">'.$errorsList[ $paramName ].'</div>';
		}
	}
	
	public function flash() {
		if ( !$this->ignore && K_Session::test('Global.FlashError') ) {			
			echo '<div class="ui-widget">';
			echo '<div style="padding: 5px;" class="ui-state-error ui-corner-all">';
			echo '<p><span style="float: left; margin-right: 3px;" class="ui-icon ui-icon-alert"></span>';
			echo '<strong>Error:</strong>'.K_Session::get('Global.FlashError').'</p>';
			echo '</div></div><br/>';
			K_Session::remove('Global.FlashError');
		}
	
	}
}

?>