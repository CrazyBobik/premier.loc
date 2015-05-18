<?php

class Blocks_Controller_Onmain  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {
		
			
		$onMainCats = K_TreeQuery::crt('/onmaincats/'.Allconfig::$contentLang.'/')->types(array('productlink'))
																				->joins(array('productlink'=>array(
																						'product'=>'product'
																						
																					   )
																					)
																				)->go();
									
			
		//var_dump($onMainCats);	
		
		/*
		$menu = K_TreeQuery::crt('/menu/')->types(array('link','menucat','productlink'))
		   ->joins(array('productlink'=>array(
													'product'=>'product'
													
												   )
												)
											)
			->go() ;
				
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
		*/
		
		foreach($onMainCats as $v){
			
			
			$prod = K_TreeQuery::gOne($v['type_product_id'], 'product');
		
			$prod['tree_link_sub'] = rtrim(str_replace('/products/'.Allconfig::$contentLang, '', $prod['tree_link']), '/');
			
			
			$this->view->products[]=$prod;
			
		}
		
		$this->render('onmain'); 
 	}

}