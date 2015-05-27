<?php

class Ajax_Controller_LoadSlider  extends K_Controller_Ajax {

    public function topsliderAction(){

        $html = K_Request::call(array(
                'module'     => 'blocks',
                'controller' => 'slider',
                'action'     => 'index',
                'params'     => array('node' => $node)
            )
        );

        $this->putAjax($html);
    }
}