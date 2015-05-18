<?php 

/**
 * Widget Helper
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

class includeHelper {
	public function template( $path ) {       
            if ( file_exists($path) ) {
                require_once $path;
            }
        }
	
}
