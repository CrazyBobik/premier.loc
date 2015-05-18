<?php

class Site_Model_AdsImport extends Site_Model_Ads {
    
    var $name = 'ads';
    var $primary = 'id';
	
	var $isImportInit = null;	
		
	public function importInit() {
	
		if ($this->isImportInit) {
		
			return;
		
		}
		
		$this->init();
		
		$this->importRegion = k_q::columnArrayName("select id,name from region");
		$this->importZone = k_q::columnArrayName("select id,name from zone");
		$this->importCity = k_q::columnArrayNameZone("select id,name,zone2 from city");
		$this->importZone2 = k_q::columnArrayName("select id,name from zone2");
		// простые города
		$this->importSimplCity = k_q::columnArrayName("select id,name from city where zone2=0");
		// горда с микрорайонами 
		$this->importCompCity = k_q::columnArrayName("select id,name from city where zone2=1");
		
		$this->isImportInit = true;
		
	}	
   
	public function updateImport($item, $id, $selbik){
	//var_dump($id);exit();sdfsdf
	      				$this->importInit();	
						
						$findrow = k_q::row("select * from ads where id=$id");
						
						
						$impId = $item->local_realty_id; 
						//var_dump($findrow);exit();
						$cur = K_inet::getCurrency('USD');
						$cur2 = K_inet::getCurrency('EUR'); 
						
						if(empty($findrow)){
						
							$outArray = array(
								'error' => true,
								'msg' => "Объявление с id импорта №".$impId." не найденно",
								'id' => $id		
							);
								
							return $outArray;		
							
						}					
													
						$_user_imp = $findrow['user'];
						
						$findrow_user = k_q::row("select * from users where id=$_user_imp");
						
						if(isset($item->meas)){
							
							$meas = $item->meas;
					     	$data['meas'] = $this->meas($meas);	
							$meas = $data['meas'];
						
						}
						else {
							$data['meas'] = 43;	
							$meas = $data['meas'];
						}


						/*if(empty($user_imp['id'])) { 
						 $str2.$impId.'<br/>'; 
						     // return $this->end();
							 exit();
						}
						
						if(empty($user_ses['id'])) { 
						
						    $errors[] = $str2.$impId.'<br/>===';
							exit();
							//return $this->end();
						}
						
						if($user_imp['id']!=$userImportId){
						
							$user_rab=sww("","branch","boss=".$userImportId." and rab=".$user_imp['id']."","",""); 
							
							if(empty($user_rab['id'])) { 
							
								echo $str2.$impId.'<br/>'; 
								return false;	
								
							}
							$user=$user_rab['rab'];
							
						}*/
										
						//if($user_imp['id'] == $user_ses['id']) 
						
						if (isset($findrow['pub'])) {
							$pub = $findrow['pub'];
						}
						
						/*if (isset($findrow['real_pub_date'])) {
							$real_pub_date = $findrow['real_pub_date'];
						}*/
						
						$layout=$item->open_space; 	if($layout==true) $layout=1; else $layout=0;
						$garage=$item->garage; 		if($garage==true) $garage=1; else $garage=0;
						$barn=$item->barn; 			if($barn==true) $barn=1; else $barn=0;
						$gas=$item->gas; 			if($gas==true) $gas=1; else $gas=0;
						$water=$item->water;		if($water==true) $water=1; else $water=0; 
						$sewage=$item->sewage; 		if($sewage==true) $sewage=1; else $sewage=0;
												
						if (isset($findrow['price'])) {
							$price = $findrow['price'];
						}
						else {
							$price = $item->price;
						}
						
						if($price==0) $dogov=1; else $dogov=0;
						
						$price_cur = $this->cur(k_string::treat((string)$item->cur));	
						
						$torg=$item->torg;  		if($torg==true) $torg=1; else $torg=0;
						$rasr=$item->credit;  		if($rasr==true) $rasr=1; else $rasr=0;
						
						$mans=$item->mans;  		if($mans==true) $mans=1; else $mans=0;
						$cellar=$item->cellar; 		if($cellar==true) $cellar=1; else $cellar=0;
						
						$duration=$item->publish_day; $st_day=$duration; $duration=$this->publish_day($duration); 
						$enddate = strtotime(date("Y-m-d").' +90 day');
						$enddate = date('Y-m-d', $enddate);
						
						//$type_transac_dop = $item->type_ob_dop; $type_transac_dop = $this->type_ob_dop($item->type_ob_dop); 
						
						$code = $findrow_user['code']; 
						$phone = $findrow_user['phone']; 
						
						$category = $this->section_realty_id((string)$item->section_realty_id);
						
						$area1 = k_string::treat($item->all_sq);
											
						if($category==8){
							if($meas==42){ $area_s=$area1; $area_m=$area1*100; $area_g=$area1/100; } 
							else if($meas==93){  $area_s=$area1*100; $area_m=$area1*10000; $area_g=$area1;   } 
							else { $area_s=$area1/100; $area_m=$area1; $area_g=$area1/10000; } 
														
						}else { $area_s=$area1/100; $area_m=$area1; $area_g=$area1/10000; } 
						if($item->price_to>0) $prto=$item->price_to; else $prto=$item->price;
						
						if (isset($item->type_price_id)&&(!empty($item->type_price_id))) {
							$price_object = $this->type_price_id(k_string::treat((string)$item->type_price_id));
						}
						else {
							$price_object = 35;
						}
						
						if($item->price_to==0&&$$item->price==0){
							$price_uah=0;
							$price_usd=0;
							$price_eur=0;	
							$price_uah_kv=0;
							$price_usd_kv=0;
							$price_eur_kv=0;
						}else if($item->price_to>0){
							$price_uah=$this->price_tarnsform_db('uah',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd=$this->price_tarnsform_db('usd',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur=$this->price_tarnsform_db('eur',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);	
							$price_uah_kv=$this->price_tarnsform_db('uah',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd_kv=$this->price_tarnsform_db('usd',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur_kv=$this->price_tarnsform_db('eur',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
						}else{
							$price_uah=$this->price_tarnsform_db('uah',1,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd=$this->price_tarnsform_db('usd',1,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur=$this->price_tarnsform_db('eur',1,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);	
							$price_uah_kv=$this->price_tarnsform_db('uah',0,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd_kv=$this->price_tarnsform_db('usd',0,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur_kv=$this->price_tarnsform_db('eur',0,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
						}
						$date = date('Y-m-d H:i:s');
						
						if (isset($item->type_ob_id)) {
						
							$type_ob_id = $this->type_ob_id(k_string::treat((string)$item->type_ob_id));
						
						}
						elseif (isset($item->type)) {
						
							$type_ob_id = $this->type_ob_id(k_string::treat((string)$item->type));
						
						} 
						if(isset($item->type_wall_id)){	
							$walls = $this->select_id_imp("selects",k_string::treat((string)$item->type_wall_id)); 
						}
						else {
							$walls = 37;
						}
						
						if(isset($item->obmen_id)){	
							$obmen=$this->obmen($this->select_id_imp("selects",k_string::treat((string)$item->obmen_id)));
						}
						else {
							$obmen=90;
						}
						
						if (isset($item->state_id)) {
							$state = $this->select_id_imp("selects",k_string::treat((string)$item->state_id));
						}else {
							$state = 103;						
						}
						
						if (isset($item->type_object)) {
							$to = $this->select_id_imp("selects",k_string::treat((string)$item->type_object));
						}
						else {
							$to = 0;
						}
						
						$fla = $this->getFloor(intval($item->floor));
						$flb = $this->getFloor(intval($item->all_floor));
						
						if($category==8){
							
							$fla = 0;
							$flb = 0;
							$walls = 0;
							$state = 0;
							
						}
						
						if (isset($findrow['city'])) {
							$city = intval($findrow['city']);
						}
						else {
							$city = $item->city_id;
						}
												
						if(empty($city)){
						
							return false;	
							
						};
						
						$region = $item->obl_id;
														
						$zone = $item->area_id;
						
						/*$z2 = k_q::row("select * from city where id=".$city."");
						$zone = $z2['zone_id'];
						$z = k_q::row("select * from region where city_id=".$city."");
							
						if(isset($z['id'])){ 
						
							$zone2 = $zon; 
							
						}else{
						
							$city = $zon; 
							$zone2 = 0; 
							
						}*/
						//var_dump($city);
						//var_dump($city.' '.$zone.' '.$zone2);
						
						$loc = $this->getLocationImport($region, $city, $zone);
						
						$_region = $loc['region'];
						$_city = $loc['city'];
						$_zone = $loc['zone'];
						$_zone2 = $loc['zone2'];
						
						//var_dump($loc);
						
						$data = array(
							'id' => $id,
							'pub' => $pub,
							'date' => $date,
							'up_date' => $date,
							'area1_t' => k_string::treat($item->area1_t),
							'imp_id' => k_string::treat($impId),
							'plot_t' => k_string::treat($item->plot_to),
							'price_t' => k_string::treat($item->price_to),
							'rooms_t' => k_string::treat($item->kol_kom_to),
							'stock_f' => k_string::treat($item->stock_f),
							'stock_l' => k_string::treat($item->stock_l),
							'user' => k_string::treat($_user_imp),
							'layout' => k_string::treat($layout),
							'walls' => $walls,
							'garage' => k_string::treat($garage),
							'barn' => k_string::treat($barn),
							'gas' => k_string::treat($gas),
							'water' => k_string::treat($water),
							'sewage' => k_string::treat($sewage),
							'id_add' => k_string::treat($id.$impId),
							'category' => k_string::treat($category),
							'type_propert' => $this->select_id_imp("ads_subsec",k_string::treat((string)$item->type_realty_id)),
							'type_transac' => $type_ob_id,
							'region' => $_region,
							'city' => $_city,
							'zone' => $_zone,
							'zone2' => $_zone2,
							'street' => k_string::treat($item->street),
							'house' => k_string::treat($item->house),
							'rooms' => k_string::treat($item->kol_kom),
							'fla' => $fla,
							'flb' => $flb,
							'area1' => $area1,
							'area2' => k_string::treat($item->zh_sq),
							'area3' => k_string::treat($item->k_sq),
							'state' => $state,
							'price_cur' => $price_cur,
							'price' => k_string::treat($price),
							'price_object' => $price_object,
							'dogov' => k_string::treat($dogov),
							'torg' => k_string::treat($torg),
							'rasr' => k_string::treat($rasr),
							'obmen' => $obmen,
							'proposal' => 32,
							'plot' => k_string::treat($item->plot),
							'meas' => $this->meas(k_string::treat((string)$item->meas)),
							'mans' => k_string::treat($mans),
							'cellar' => k_string::treat($cellar),
							'built' => k_string::treat($item->built),
							'nazn' => $nazn=$this->nazn(k_string::treat((string)$item->nazn)),
							'cars' => k_string::treat($item->cars),
							'business' => k_string::treat($item->business),
							'sphere' => k_string::treat($item->sphere),
							'type_object' => $to,
							'text' => k_string::treat($item->description),
							'duration' => k_string::treat($duration),
							'enddate' => k_string::treat($enddate),
							//'durationval' => k_string::treat($_POST['durationval']),
							//'type_ads' => k_string::treat($_POST['type_ads']),
							//'rate' => k_string::treat($_POST['rate']),
							//'add_red' => k_string::treat($_POST['accra']),
							//'redid' => k_string::treat($_POST['redid']),
							//'ratesum' => k_string::treat($_POST['ratesum']),
							'type_transac_dop' => $this->type_ob_dop(k_string::treat((string)$item->type_ob_dop)),
							'code' => $code,
							'phone' => $phone,
							'area_s' => $area_s,
							'area_g' => $area_g,
							'area_m' => $area_m,
							'price_uah' => $price_uah,
							'price_usd' => $price_usd,
							'price_eur' => $price_eur,
							'price_uah_kv' => $price_uah_kv,
							'price_usd_kv' => $price_usd_kv,
							'price_eur_kv' => $price_eur_kv
							//'cur' => k_string::treat($_POST['cur_usd']),
							//'cur2' => k_string::treat($_POST['cur_eur']),
							
													
						);
						//var_dump($data);exit();
					    if($this->add($data, 1)){
						
							$outArray = array(
								
									'error'=> false,
									'msg' => 'Объявление успешно обновлено',
									'dateTo' => $enddate,
									'id' => $id
												
							);	
							if(isset($item->photos_urls->loc) && !empty($item->photos_urls->loc) && (is_array($item->photos_urls->loc) || is_object($item->photos_urls->loc))){	
								//var_dump($data['id_add']);
									$this->deleteGall($data['id_add']);
								
									$this->saveFotos($item->photos_urls->loc, $data['id_add'], $userImportId, 10);
									
								}
							
						}
						else {
						
							$outArray = array(
								
									'error'=> true,
									'msg' => 'Ошибка при обновлении объявления №'.$id.'',
									'erarray' => $this->getErrorsD(),
									'dateTo' => $enddate,
									'id' => $id
												
							);	
						
						}
					
			
					
						return $outArray;		
						
		}
			
	    function addImport($item, $userImportId, $selbik){
		//var_dump($item.$userImportId);
				$this->importInit();
				
						$impId = $item->local_realty_id;
						//$mail = $item->email;
						
						 $cur = K_inet::getCurrency('USD');
						 $cur2 = K_inet::getCurrency('EUR'); 
						
						$userImp = k_q::row("select * from users where id='$userImportId'");
						
						if($selbik){
						
							$id = k_q::one('SELECT id FROM ads WHERE selbik="1" AND imp_id ='.$impId);
						
						}else{
						
							$id = k_q::one('SELECT id FROM ads WHERE user="'.$userImportId.'" AND imp_id ='.$impId);
						
						}
						if($id){
								
							$outArray = array(
								'error' => true,
								'msg' => "Объявление с id импорта №".$impId." уже было импортировано",
								'id' => $id		
							);
								
							return $outArray;		
						
						}	
						
						/*if(empty($userImp['id'])) { 
						
						    $outArray = array(
								'error' => true,
								'msg' => "ID пользователя не найдено",
								'label' => "ID пользователя",
								'id' => $id		
							);
								
							return $outArray;	
							
						}*/							

						if(isset($item->meas)){
							
							$meas = $item->meas;
					     	$data['meas'] = $this->meas($meas);	
							$meas = $data['meas'];
						
						}
						else {
							$data['meas'] = 43;	
							$meas = $data['meas'];
						}
												
						if($userImp['id']!=$userImportId){
						
							$user_rab=sww("","branch","boss=".$userImportId." and rab=".$userImp['id']."","",""); 
							
							if(empty($user_rab['id'])) { 
							
								echo $str2.$impId.'<br/>'; 
								return false;	
								
							}
							$user=$user_rab['rab'];
							
						}
										
						if($userImp['id']!='') 
						$user = $userImp['id'];
												
						$layout=$item->open_space; 	if($layout==true) $layout=1; else $layout=0;
						$garage=$item->garage; 		if($garage==true) $garage=1; else $garage=0;
						$barn=$item->barn; 			if($barn==true) $barn=1; else $barn=0;
						$gas=$item->gas; 			if($gas==true) $gas=1; else $gas=0;
						$water=$item->water;		if($water==true) $water=1; else $water=0; 
						$sewage=$item->sewage; 		if($sewage==true) $sewage=1; else $sewage=0;
											
						if($item->price==0) $dogov=1; else  $dogov=0;	
						
						$price_cur = $this->cur(k_string::treat((string)$item->cur));
						
						$torg=$item->torg;  		if($torg==true) $torg=1; else $torg=0;
						$rasr=$item->credit;  		if($rasr==true) $rasr=1; else $rasr=0;
						
						$mans=$item->mans;  		if($mans==true) $mans=1; else $mans=0;
						$cellar=$item->cellar; 		if($cellar==true) $cellar=1; else $cellar=0;
						
						$duration=$item->publish_day; $st_day=$duration; $duration=$this->publish_day($duration); 
						$enddate = strtotime(date("Y-m-d").' +90 day');
						$enddate = date('Y-m-d', $enddate);
						
						//$type_transac_dop = $item->type_ob_dop; $type_transac_dop = $this->type_ob_dop($item->type_ob_dop); 
						
						$code = k_string::treat($userImp['code']); 
						$phone = k_string::treat($userImp['phone']); 
						
						$category = $this->section_realty_id((string)$item->section_realty_id);
						
						$area1 = k_string::treat($item->all_sq);
											
						if($category==8){
							if($meas==42){ $area_s=$area1; $area_m=$area1*100; $area_g=$area1/100; } 
							else if($meas==93){  $area_s=$area1*100; $area_m=$area1*10000; $area_g=$area1;   } 
							else { $area_s=$area1/100; $area_m=$area1; $area_g=$area1/10000; } 
														
						}else { $area_s=$area1/100; $area_m=$area1; $area_g=$area1/10000; } 
						if($item->price_to>0) $prto=$item->price_to; else $prto=$item->price;
						
						if (isset($item->type_price_id)&&(!empty($item->type_price_id))) {
							$price_object = $this->type_price_id(k_string::treat((string)$item->type_price_id));
						}
						else {
							$price_object = 35;
						}
						
						if($item->price_to==0&&$$item->price==0){
							$price_uah=0;
							$price_usd=0;
							$price_eur=0;	
							$price_uah_kv=0;
							$price_usd_kv=0;
							$price_eur_kv=0;
						}else if($item->price_to>0){
							$price_uah=$this->price_tarnsform_db('uah',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd=$this->price_tarnsform_db('usd',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur=$this->price_tarnsform_db('eur',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);	
							$price_uah_kv=$this->price_tarnsform_db('uah',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd_kv=$this->price_tarnsform_db('usd',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur_kv=$this->price_tarnsform_db('eur',2,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
						}else{
							$price_uah=$this->price_tarnsform_db('uah',1,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd=$this->price_tarnsform_db('usd',1,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur=$this->price_tarnsform_db('eur',1,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);	
							$price_uah_kv=$this->price_tarnsform_db('uah',0,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_usd_kv=$this->price_tarnsform_db('usd',0,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
							$price_eur_kv=$this->price_tarnsform_db('eur',0,$prto,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas);
						}
						$pub = $real_pub_date = $date = date('Y-m-d H:i:s'); //дата публикации-дата создания-дата обновления(устаревшая)
						
						if (isset($item->type_ob_id)) {
						
							$type_ob_id = $this->type_ob_id(k_string::treat((string)$item->type_ob_id));
						
						}
						elseif (isset($item->type)) {
						
							$type_ob_id = $this->type_ob_id(k_string::treat((string)$item->type));
						
						} 
						if(isset($item->type_wall_id)){	
							$walls = $this->select_id_imp("selects",k_string::treat((string)$item->type_wall_id)); 
						}
						else {
							$walls = 37;
						}
						
						if(isset($item->obmen_id)){	
							$obmen=$this->obmen($this->select_id_imp("selects",k_string::treat((string)$item->obmen_id)));
						}
						else {
							$obmen=90;
						}
						
						if (isset($item->state_id)) {
							$state = $this->select_id_imp("selects",k_string::treat((string)$item->state_id));
						}else {
							$state = 103;						
						}
						
						if (isset($item->type_object)) {
							$to = $this->select_id_imp("selects",k_string::treat((string)$item->type_object));
						}
						else {
							$to = 0;
						}
						
						
						
						$fla = $this->getFloor(intval($item->floor));
						$flb = $this->getFloor(intval($item->all_floor));
						
						if($category==8){
							
							$fla = 0;
							$flb = 0;
							$walls = 0;
							$state = 0;
							
						}
						
						$region = $item->obl_id;
						
						$city = $item->city_id;
								
						$zone = $item->area_id;
						
						/*$z2 = k_q::row("select * from city where id=".$city."");
						$zone = $z2['zone_id'];
						$z = k_q::row("select * from region where city_id=".$city."");
							
						if(isset($z['id'])){ 
						
							$zone2 = $zon; 
							
						}else{
						
							$city = $zon; 
							$zone2 = 0; 
							
						}*/
						//var_dump($city);
						//var_dump($city.' '.$zone.' '.$zone2);
						
						$loc = $this->getLocationImport($region, $city, $zone);
						
						$_region = $loc['region'];
						$_city = $loc['city'];
						$_zone = $loc['zone'];
						$_zone2 = $loc['zone2'];
						
						var_dump($loc);
												
						$data = array(
				
							'pub' => $pub,
							'real_pub_date' => $real_pub_date,
							'date' => $date,
							'area1_t' => k_string::treat($item->area1_t),
							'imp_id' => k_string::treat($impId),
							'plot_t' => k_string::treat($item->plot_to),
							'price_t' => k_string::treat($item->price_to),
							'rooms_t' => k_string::treat($item->kol_kom_to),
							'stock_f' => k_string::treat($item->stock_f),
							'stock_l' => k_string::treat($item->stock_l),
							'user' => k_string::treat($userImportId),
							'layout' => k_string::treat($layout),
							'walls' => $walls,
							'garage' => k_string::treat($garage),
							'barn' => k_string::treat($barn),
							'gas' => k_string::treat($gas),
							'water' => k_string::treat($water),
							'sewage' => k_string::treat($sewage),
							'id_add' => k_string::treat($userImportId.$impId),
							'category' => k_string::treat($category),
							'type_propert' => $this->select_id_imp("ads_subsec",k_string::treat((string)$item->type_realty_id)),
							'type_transac' => $type_ob_id,
							'region' => $_region,
							'city' => $_city,
							'zone' => $_zone,
							'zone2' => $_zone2,
							'street' => k_string::treat($item->street),
							'house' => k_string::treat($item->house),
							'rooms' => k_string::treat($item->kol_kom),
							'fla' => $fla,
							'flb' => $flb,
							'area1' => $area1,
							'area2' => k_string::treat($item->zh_sq),
							'area3' => k_string::treat($item->k_sq),
							'state' => $state,
							'price_cur' => $price_cur,
							'price' => k_string::treat($item->price),
							'price_object' => $price_object,
							'dogov' => k_string::treat($dogov),
							'torg' => k_string::treat($torg),
							'rasr' => k_string::treat($rasr),
							'obmen' => $obmen,
							'proposal' => 32,
							'plot' => k_string::treat($item->plot),
							'meas' => $this->meas(k_string::treat((string)$item->meas)),
							'mans' => k_string::treat($mans),
							'cellar' => k_string::treat($cellar),
							'built' => k_string::treat($item->built),
							'nazn' => $nazn=$this->nazn(k_string::treat((string)$item->nazn)),
							'cars' => k_string::treat($item->cars),
							'business' => k_string::treat($item->business),
							'sphere' => k_string::treat($item->sphere),
							'type_object' => $to,
							'text' => k_string::treat($item->description),
							'duration' => k_string::treat($duration),
							'enddate' => k_string::treat($enddate),
							//'durationval' => k_string::treat($_POST['durationval']),
							//'type_ads' => k_string::treat($_POST['type_ads']),
							//'rate' => k_string::treat($_POST['rate']),
							//'add_red' => k_string::treat($_POST['accra']),
							//'redid' => k_string::treat($_POST['redid']),
							//'ratesum' => k_string::treat($_POST['ratesum']),
							'type_transac_dop' => $this->type_ob_dop(k_string::treat((string)$item->type_ob_dop)),
							'code' => $code,
							'phone' => $phone,
							'area_s' => $area_s,
							'area_g' => $area_g,
							'area_m' => $area_m,
							'price_uah' => $price_uah,
							'price_usd' => $price_usd,
							'price_eur' => $price_eur,
							'price_uah_kv' => $price_uah_kv,
							'price_usd_kv' => $price_usd_kv,
							'price_eur_kv' => $price_eur_kv
							//'cur' => k_string::treat($_POST['cur_usd']),
							//'cur2' => k_string::treat($_POST['cur_eur']),
							
													
						);
						
						
						var_dump($data);
						
						/*$type_transac = $this->type_ob_id((string)$item->type_ob_id);
					
						if(empty($type_transac)){
						
							$type_transac=$item->type; $type_transac = $this->type_ob_id($type_transac);
						
						}*/
						
						
							
							/*
							if($adsLimit>=$maxLimit){
								 echo $str4.$impId.$str5;  continue;
							}
							*/		
			/*
							mysql_query("insert into ads(
								id_add,price_uah,price_eur,price_usd,price_uah_kv,price_eur_kv,price_usd_kv,area_s,area_m,area_g,
								region,city,zone,zone2,user, imp_id,st_day,	area1_t,plot_t,price_t,rooms_t,layout,garage,barn,gas,water,sewage,category,
								type_propert,type_transac,street,house,rooms,walls,fla,flb,area1,area2,area3,state,price_cur,
								price,price_object,dogov,torg,rasr,obmen,proposal,plot,meas,mans,cellar,nazn,type_object,text,
								code, phone, duration, type_transac_dop, enddate, pub, date, selbik
							) values (
								'$id_add','$price_uah','$price_eur','$price_usd','$price_uah_kv','$price_eur_kv','$price_usd_kv','$area_s','$area_m','$area_g',
								'$region','$city','$zone','$zone2',$user, '$impId','$st_day','$area1_t','$plot_t','$price_t','$rooms_t','$layout','$garage','$barn',
								'$gas','$water','$sewage','$category','$type_propert','$type_transac','$street','$house',
								'$rooms','$walls','$fla','$flb','$area1','$area2','$area3','$state','$price_cur',
								'$price','$price_object','$dogov','$torg','$rasr','$obmen','$proposal','$plot','$meas',
								'$mans','$cellar','$nazn','$type_object','$text','$code','$phone','$duration','$type_transac_dop',
								'$enddate',NOW(),NOW(),".($selbik ? '1' : '0')."
							)");
							
							$adsID = mysql_insert_id();
							*/
							
							if($this->add($data, true)){
							
								mysql_query("update users set colpub=colpub-1,colpub_all=colpub_all+1 where id=".$userImportId."");
						
								$outArray = array(
									
										'error'=> false,
										'msg' => 'Объявление с локальным номером '.$impId.' успешно импортированно',
										'dateTo' => $enddate,
										'id' => $id
													
								);	
								if(isset($item->photos_urls->loc) && !empty($item->photos_urls->loc) && (is_array($item->photos_urls->loc) || is_object($item->photos_urls->loc))){	
								
									$this->saveFotos($item->photos_urls->loc, $data['id_add'], $userImportId, 10);
									
								}
								
								$countnew++;
								
							}
							else {
							
								$outArray = array(
									
										'error'=> true,
										'msg' => 'Ошибка при добавлении объявления №'.$impId.'',
										'erarray' => $this->getErrorsD(),
										'dateTo' => $enddate,
										'id' => $id
													
								);	
							
							}
						
							return $outArray;	
							   	

	 }
       
    function saveFotos($fotoArray, $id_add, $impOwnerId , $limit){
      //var_dump($fotoArray);
        	if(count($fotoArray)>0){
        	                 
                $this->deleteGall($id_add);   
		        $i = 0; 
                    
				foreach($fotoArray as $img){ 
				
					$img = (string)$img;
				
		              
						$igmName = end(explode("/", $img));
                        
                        //сохраняем картинку в папку с орегинальными картинками
						$file = AllConfig::$adsImgPaths['temp'].$igmName;
						
						if(k_inet::saveFile($img, $file)){
						  
							$newName = $impOwnerId.'_'.date("Ymd").rand(1000000000,9999999999).'.jpg';
						
                            $this->genImages( $file, $newName );
                            
							K_q::query("insert into gallery (img,imp,id_add) values ('$newName','$img','$id_add')");
						
							if($i==1){
							     
								$product_img =  K_q::lastId();
                                
							    K_q::query("update ads set img='$product_img' where id_add=".$id_add."");
								
							}
							
							
						}
                        
						$i++;	
						if(isset($limit) && $limit>0 && $i>$limit)break;
							
				}
						
			}
            
    }  
	
	function getFloor($floor) {
		
		switch($floor) {
			case 1: $_floor = 12; break;
			case 2: $_floor = 13; break;
			case 3: $_floor = 14; break;
			case 4: $_floor = 15; break;
			case 5: $_floor = 16; break;
			case 6: $_floor = 17; break;
			case 7: $_floor = 18; break;
			case 8: $_floor = 19; break;
			case 9: $_floor = 20; break;
			case 10: $_floor = 21; break;
			case 11: $_floor = 22; break;
			case 12: $_floor = 23; break;
			case 13: $_floor = 24; break;
			case 14: $_floor = 25; break;
			case 15: $_floor = 26; break;
			case 16: $_floor = 27; break;
			case 17: $_floor = 28; break;
			case 18: $_floor = 29; break;
			case 19: $_floor = 30; break;
			case 20: $_floor = 94; break;
			case 21: $_floor = 118; break;
			case 22: $_floor = 119; break;
			case 23: $_floor = 120; break;
			case 24: $_floor = 121; break;
			case 25: $_floor = 122; break;
			case 26: $_floor = 123; break;
			case 27: $_floor = 124; break;
			case 28: $_floor = 125; break;
			case 29: $_floor = 177; break;
			case 30: $_floor = 126; break;
			case 31: $_floor = 142; break;
			case 32: $_floor = 143; break;
			case 33: $_floor = 144; break;
			case 34: $_floor = 145; break;
			case 35: $_floor = 146; break;
			case 36: $_floor = 147; break;
			case 37: $_floor = 148; break;
			case 38: $_floor = 149; break;
			case 39: $_floor = 150; break;
			case 40: $_floor = 151; break;
			case 'цокольный': $_floor = 11; break;
		}
		
		return $_floor;
		
	}
      
	function getRegionId($regionName) {
	
		$this->importInit();
		
		if($this->importRegion[$regionName]) {
		
			return $this->importRegion[$regionName];
			
		}
		else {
		
			return 0;
		
		}
	
	}  
	  	  
	// Функция которая получает id города по его названию.

	function getCityId($cityName){
	
		$this->importInit();
		
		if($this->importCity[$cityName]) {
		
			return $this->importCity[$cityName];
			
		}
		else {
		
			return 0;
		
		}
	}
	
	// Функция которая возвращяет id района города по его названию.
	function getZone2Id($areaName){
	
		$this->importInit();
		
		if($this->importZone2[$areaName]) {
		
			return $this->importZone2[$areaName];
			
		}
		else {
		
			$arr = $this->getCityId($areaName);
			return $arr['id'];
		
		}
	}
	
	// Функция которая возвращяет id района области по его названию.
	function getZoneId($areaName){
	
		$this->importInit();
		
		if($this->importZone2[$areaName]) {
		
			return $this->importZone2[$areaName];
			
		}
		else {
		
			$arr = $this->getCityId($areaName);
			return $arr['id'];
		
		}
	}
		
	/* Функция которая возвращяет id района города по его названию.
	
	// Варианты:
	1) $cityName - областной центр
	   $areaName - Микрорайон областного центра
	   
	2) $cityName - Район области
	   $areaName - Село, населённый пункт
	
	*/	

	public function getLocationImport($region, $subRegion, $location){
				
		$this->importInit();
		
		//первоначальная фильтрация что-бы ключи работали 
		
		$region = k_string::forKey($region);
		$subRegion = k_string::forKey($subRegion);
		$location = k_string::forKey($location);

			if(!is_numeric($region)){
			
				$region = $this->getRegionId($region);
			
			}
								
			if($this->importCompCity[$subRegion]){ //значит что у нас областной центр и в $location у нас микрорайон
				
				$city = $this->importCompCity[$subRegion]; // устанавливаем первым элементом массива ответа айди города
	
				if($this->importZone2[$location]){ // узнаём микрорайон
			
					$zone2 = $this->importZone2[$location];
					
				}
				else {
				
					$zone2 = 0;// микрорайон не известен 
			
				}
				$zone = 0;// район области нам не важен в этом случае
			} 
			elseif($this->importZone[$subRegion]){// Проверяем записан ли у нас район области zone в суб регионе
			
				$zone = $this->importZone[$subRegion];

				//в этом случае в $location у нас село или пгт, выбираем его из таблицы city
				
				$city = $this->importSimplCity[$location];
				
				// и $zone2 нам не нужен
				
				$zone2 = 0;// микрорайон не нужен так как его нет
				
			}else{ // $subRegion не установлен или не определяеться
			
				//ищем его по $location

				if($this->importZone2[$location]){ // ищим в микрорайонах соотвественно $subRegion - большой город
					
					$zone2 = $this->importZone2[$location];
					// ищем большой город по микрорайону
					$city = $this->zone2City[$zone2];
					
				}elseif($this->importSimplCity[$location]){ // ищим в Городах соотвеcтвенно $subRegion - район области
				
					$city = $this->importSimplCity[$location];
					// ищем район области по (городу, селу) в указанной области 
					$zone = $this->zone2City[$region.'-'.$city];
				
				}else{// ничего не нашли всё по нулям 
				
					$city = 0;
					$zone = 0;
					$zone2 = 0;
				
				}
			
			}
			
					var_dump($region.' '.$city.' '.$zone.' '.$zone2);

	
		return array('region'=>$region,
					 'city'=>$city,
					 'zone'=>$zone,
					 'zone2'=>$zone2);
	}
 
	function type_ob_id($str){
		switch($str){
			case 'Продать': $str=89; break;
			case 'Сдать': $str=88; break;
			case 'Купить': $str=92; break;
			case 'Cнять': $str=87; break;
		}
		return $str;
	}

	function type_ob_dop($str){
		switch($str){
			case 'Долгосрочно': $str=85; break;
			case 'Посуточно': $str=84; break;
		}
		return $str;
	}

	function section_realty_id($str){
		switch($str){
			case 'Квартиры': $str=1; break;
			case 'Дома': $str=3; break;
			case 'Гаражи': $str=5; break;
			case 'Коммерческая недвижимость': $str=6; break;
			case 'Земельные участки': $str=8; break;
		}
		return $str;
	}

	function cur($str){
		switch($str){
			case 'Гривна': $str=6; break;
			case 'Доллар': $str=5; break;
			case 'Евро': $str=95; break;
		}
		return $str;
	}

	function type_price_id($str){
		switch($str){
			case 'за все': $str=35; break;
			case 'за кв. м.': $str=36; break;
		}
		return $str;
	}

	function type_proposal($str){
		switch($str){
			case 'От хозяина': $str=31; break;
			case 'От посредника': $str=32; break;
		}
		return $str;
	}

	function nazn($str){
		switch($str){
			case 'Грузовое': $str=68; break;
			case 'Легковое': $str=69; break;
		}
		return $str;
	}

	function meas($str){
		switch($str){
			case 'Гектар': $str=93; break;
			case 'Сотка': $str=42; break;
			case 'кв.м.': $str=43; break;
			case 'Га': $str=93; break;
			case 'га.': $str=93; break;
			case 'сот': $str=42; break;
			case 'сот.': $str=42; break;
			case 'кв.м': $str=43; break;
		}
		return $str;
	}

	function publish_day($str){
		switch($str){
			case '7': $str=7; break;
			case '14': $str=8; break;
			case '30': $str=9; break;
		}
		return $str;
	}

	function select_id_imp($table, $title){

		if($title=="квартира"){
			return 24;
		}
		if($title=="Кафе,ресторан"){
			$title="Кафе, ресторан";
		}
		if($title=="Сфера услуг"){
			$title="Cфера услуг";
		}
		if($title=="Отель,гостиница"){
			$title="Отель, гостиница";
		}
		if($title=="Комнаты"){
			$title="Комната";
		}
		if($title=="Квартиры"){
			$title="Квартира";
		}
		
		$r=sww("","$table","title='$title'","","");
		return $r['id'];
		
	}

	function obmen($str){
		if($str==90||$str==91||$str==105||$str==106||$str==107||$str==108) $str=$str; else $str=90;
		return $str;
	}
	
	function price_tarnsform_db($p,$p2,$price,$price_cur,$cur,$cur2,$price_object,$area1,$category,$meas){
	//vd1($p.' '.$p2.' '.$price.' '.$price_cur.' '.$cur.' '.$cur2.' '.$price_object.' '.$area1.' '.$category.' '.$meas);
	   if(stristr($area1, ',') == TRUE) {
		$area1expl = explode(',', $area1);
		$area1 = $area1expl[0].'.'.$area1expl[1];
	   }
	   if(stristr($area2, ',') == TRUE) {
		$area2expl = explode(',', $area2);
		$area2 = $area2expl[0].'.'.$area2expl[1];
	   }	
	   if(stristr($area3, ',') == TRUE) {
		$area3expl = explode(',', $area3);
		$area3 = $area3expl[0].'.'.$area3expl[1];
	   }
	
		if($price_cur==6){
			switch($p){
				case 'uah': $str=$price; break;
				case 'usd': $str=$price/$cur; break;
				case 'eur': $str=$price/$cur2; break;
			}
		}
		if($price_cur==5){
			switch($p){
				case 'uah': $str=$price*$cur; break;
				case 'usd': $str=$price; break;
				case 'eur': $str=($price*$cur)/$cur2; break;
			}
		}
		if($price_cur==95){
			switch($p){
				case 'uah': $str=$price*$cur2; break;
				case 'usd': $str=($price*$cur2)/$cur; break;
				case 'eur': $str=$price; break;
			}
		}
		if($category==8){
			switch($meas){
				case 43: $area1=$area1; break;
				case 42: $area1=$area1*100; break;
				case 93: $area1=$area1*10000; break;
			}
		}
		if($price_object==0||$price_object=='') $price_object=35;
		if($p2==1){
			if($price_object==35){
				$str=$str;
			}else{
				$str=$str*$area1;
			}
		}
		if($p2==0){
			if($price_object==35){
				$str=$str/$area1;
			}else{
				$str=$str;
			}
		}
		return round($str);
	}
	

}

?>