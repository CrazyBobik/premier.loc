<?php 

/**
 * Form Helper
 * <example> // in template
 	<?php $this->form->begin(); ?>
	<?php $this->form->text( 'core_page_id' ); ?>
	<?php $this->form->select( 'selecttest', array('123'=>'123', '345'=>'345'), '', array() ); ?>
	<?php $this->form->checkbox( 'ch1', true ); ?>
	<?php $this->form->textarea( 'ta1', null, array('cols'=>80, 'rows'=>40) ); ?>
	<?php $this->form->submit(); ?>
	<?php $this->form->end(); ?>
 * </example>
 */

//K_Loader::load('Helpers/IFormHelper');

class urlHelper {
	
	public function build( $baseUrl = '', $params = array(), $ignoreParams = array() ) {
                $url = $baseUrl;
		if ( count($params) ) {
			foreach( $params as $key => &$value ) {
				if ( count($ignoreParams) && in_array( $key, $ignoreParams ) ) {
					continue;
				}
                                if ( is_numeric($key) ) {
                                    $url .= urlencode($value).'/';
                                } else {
                                    $url .= $key.'/'.urlencode($value).'/';
                                }
			}
		}
		return $url;
	}
	
}

?>