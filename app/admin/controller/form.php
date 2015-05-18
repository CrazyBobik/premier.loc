<?php

defined( 'K_PATH' ) or die( 'DIRECT ACCESS IS NOT ALLOWED' );

class Admin_Controller_Form extends Controller {

    public function saveAction() {
        $form_key = $this->getParam( 'key' );
        $type_form = new Admin_Model_Form;
        $form_key = $this->getParam( 'key' );
        $form_post_array = isset( $_POST['frmb'] ) ? $_POST : false;

        if ( $form_post_array != false ) {

            K_Loader::load( 'formbuilder', APP_PATH . '/plugins' );
            $form_builder = new Formbuilder( $form_post_array );

            $form_array = $form_builder->get_encoded_form_array();

            $form_data = array( 'type_form_id' => $form_key, 'type_form_content' => serialize( $form_array ) );

            $type_form->save( $form_data );

        }
        $this->putAjax( 'OK' );
    }

    public function loadAction() {

        $type_form = new Admin_Model_Form;

        $form_key = $this->getParam( 'key' );

        $form_data = $type_form->fetchRow( K_Db_Select::create()->where( "type_form_id=$form_key" ) );

        if ( $form_data ) {

            K_Loader::load( 'formbuilder', APP_PATH . '/plugins' );
            $form_builder = new Formbuilder( unserialize( $form_data['type_form_content'] ) );

            $this->putAjax( $form_builder->render_json() );
        } else {
            $this->putAjax( 'ERROR' );
        }
    }

    public function previewAction() {
        $form_post_array = isset( $_POST['frmb'] ) ? $_POST : false;

        if ( $form_post_array != false ) {
            K_Loader::load( 'formbuilder', APP_PATH . '/plugins' );
            $form_builder = new Formbuilder( $form_post_array );
            $this->putAjax( $form_builder->generate_html() );
        } else {
            $this->putAjax( 'ERROR' );
        }
    }
    
   public function loadChildsAction() {
    $nodeChilds=array();
    if(intval($_POST['treeid'])){
      $nodeChilds=K_Tree::getChilds(intval($_POST['treeid']));
    }    
    
    $field='tree_'.$_POST['field'];
            $returnJSON=array();
            foreach($nodeChilds as $v){
                $id=$v['tree_id'];
                $child['title']=$v["tree_title"];
                $child['value']=$v[$field];
                
                $returnJSON[$id]=$child;    
            }  
            
       $this->putJSON($returnJSON);     
   }
}
