<?php

class Ajax_Controller_LoadMap  extends K_Controller_Ajax {

    public function mapAction(){

        $html = '';

        $items = K_TreeQuery::crt('/allcountry/')->types(array('region','city'))->go();

        foreach ($items as $v) {
            if ($_GET['id'] == $v['tree_pid']) {
                $clazz = $v['tree_type'] == 'city' ? 'city-item' : 'region-item';

                $html = $html . '<a href="javascript:false">
                <div class="'.$clazz.'" data-id="'.$v['tree_id'].'">' . $v['name'] . '</div>
                </a>';
            }
        }

        $this->putAjax($html);
    }

    public function poligonsAction(){
        $returnJSON = array();

        $result = K_Q::data('SELECT * FROM poligons');

        foreach ($result as $p){
            $id = $p['name'];
            $childs['poligons'] = $p['poligons'];

            $returnJSON[$id] = $childs;
        }

        $this->putAjax($returnJSON);
    }
}