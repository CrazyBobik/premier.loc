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

//K_Loader::load('Helpers/IFormHelper');

class widgetHelper {
	
	public function get( $tag, $attr = 'content' ) {            
            $tag = trim($tag);
            $attrName = 'widget_'.$attr;
            
            if ( empty($tag) ) {
                return;
            }
            
            $unlimCache = K_Cache_Manager::get('unlim');
            
            $blockCacheID = 'widget_'.$tag;
            
            if ( $unlimCache->test( $blockCacheID ) ) {
                $cacheData = $unlimCache->load( $blockCacheID );
                if ( isset($cacheData[ $attrName ]) ) {
                    echo $cacheData[ $attrName ];
                }
            } else {
                K_Loader::load('widgets', APP_PATH.'/default/model/');
                $widgetsTable = new widgetsModel();
                
                $widgetInfo = $widgetsTable->fetchRow(
                        K_Db_Select::create()
                            ->where( array('widget_tag' => $tag) )
                            ->limit(1)
                );
                
                if ( count($widgetInfo) ) {
                    $unlimCache->save( $blockCacheID, $widgetInfo );
                    if ( isset($widgetInfo[ $attrName ]) ) {
                        echo $widgetInfo[ $attrName ];
                    }
                }
            }
        }
	
}
