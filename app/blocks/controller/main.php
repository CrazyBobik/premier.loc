<?php

class Blocks_Controller_Main  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $menu1 = K_TreeQuery::crt("/slider1/")->type()->go(array('aliases' => true));
        $menu2 = K_TreeQuery::crt("/slider2/")->type()->go(array('aliases' => true));

        $this->view->slider = $this->structur($menu1);
        $this->view->slider2 = $this->structur($menu2);

		$this->render('main'); 
 	}

    private function structur($menu){
        $n = count($menu);
        for($i=0; $i<$n; $i++){
            for($j=$i+1; $j<$n; $j++){
                if($menu[$j]['tree_level']>$menu[$i]['tree_level']) list($menu[$i],$menu[$j]) = array($menu[$j],$menu[$i]);

            }
            $ids[]=$menu[$i]['tree_id'];
        }

        foreach($menu as $v){

            if(isset($tempArr[$v['tree_id']])){

                ksort($tempArr[$v['tree_id']]);
                $v['tree_childs']=$tempArr[$v['tree_id']];

            }

            if(!in_array($v['tree_pid'],$ids)){//если они ни кому не принадлежат

                $menu2arr[$v['tree_lkey']]=$v;
            }

            $tempArr[$v['tree_pid']][$v['tree_lkey']]=$v;

        }

        ksort($menu2arr);

        return $menu2arr;
    }
}