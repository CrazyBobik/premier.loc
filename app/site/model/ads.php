<?php

class Site_Model_Ads extends Model {
    
    var $name = 'ads';
    var $primary = 'id';
	var $type_transac_dop = array();
	var $category = array();
	var $type_propert = array();
	var $region = array();
	var $zone = array();
	var $city = array();
	var $state = array();
	var $fla = array();
	var $flb = array();
	var $obmen = array();
	var $proposal = array();
	var $walls = array();
	var $price_cur = array();
	var $price_object = array();
	var $cityZone = array();
	var $zone2City = array();
	
	var $isInit = null;
	
	
	
	static private $inst = null;

	public function init() {
	
		if ($this->isInit) {
		
			return;
		
		}
	
		$this->type_transac_dop = k_q::columnArray("select id from ks_values");
		$this->category = k_q::columnArray("select id from ads_sec");
		$this->type_propert = k_q::columnArrayValue("select id,sec from ads_subsec", 'sec');
		$this->region = k_q::columnArray("select id from region");
		$this->zone = k_q::columnArrayValue("select id,region_id from zone", 'region_id');
		$this->city = k_q::columnArrayValue("select id,zone_id from city", 'zone_id');
		$this->zone2 = k_q::columnArrayValue("select id,city_id from zone2", 'city_id');
		$this->state = k_q::columnArray("select id from selects where sel=6");
		$this->fla = k_q::columnArray("select id from selects where sel=5");
		$this->flb = k_q::columnArray("select id from selects where sel=5");
		$this->obmen = k_q::columnArray("select id from selects where sel=25");
		$this->proposal = k_q::columnArray("select id from selects where sel=7");
		$this->walls = k_q::columnArray("select id from selects where sel=4");
		$this->price_cur = k_q::columnArray("select id from selects where sel=1");
		$this->price_object = k_q::columnArray("select id from selects where sel=2");
		
		$rows = k_q::query("select id, zone_id, region_id from city where zone=0"); //соответствие сёл районам

		foreach($rows as $v){
			
			$this->cityZone[$v['region_id'].'-'.$v['id']] = $v['zone_id'];

		}
		
		$rows = k_q::query("select id, city_id from zone2"); //соответствие микрорайонов городам

		foreach($rows as $v){
			
			$this->zone2City[$v['id']] = $v['city_id'];

		}		
	
		$this->isInit = true;
		
	}
  	        
    public function deleteGall($id_add){
        
               $gall = K_q::query("SELECT * FROM gallery WHERE id_add='".$id_add."'");
                   
               foreach($gall as $v){ 
                
                    if(file_exists(Allconfig::$adsImgPaths['original'].$v['img'])){
                                unlink(Allconfig::$adsImgPaths['original'].$v['img']);
                    }
                    if(file_exists(Allconfig::$adsImgPaths['big'].$v['img'])){
                                unlink(Allconfig::$adsImgPaths['big'].$v['img']);
                    }
                    if(file_exists(Allconfig::$adsImgPaths['thumb'].$v['img'])){
                                unlink(Allconfig::$adsImgPaths['thumb'].$v['img']);
                    }
                
               };
              
               K_q::query("DELETE FROM gallery WHERE id_add='".$id_add."'");      
     }
     
    public function deleteAds($id){
        
               $ads = $this->findId($id);
               
               if($ads){
                
                   $this->deleteGall($ads['id_add']);
                   $this->removeID($id);
                   
               }else{
                
                   return false;
                     
               } 
     }     
     
	public function deleteAdsGroup($id_array){
	
               $gall = K_q::query("SELECT g.id_add id_add,g.img img FROM ads a LEFT JOIN gallery g ON a.id_add=g.id_add WHERE a.id IN (".implode(',',$id_array).")");
			   
			   $id_ads_array = array();
			   
				foreach($gall as $v){ 
                
                    if(file_exists(Allconfig::$adsImgPaths['original'].$v['img'])){
                                unlink(Allconfig::$adsImgPaths['original'].$v['img']);
                    }
                    if(file_exists(Allconfig::$adsImgPaths['big'].$v['img'])){
                                unlink(Allconfig::$adsImgPaths['big'].$v['img']);
                    }
                    if(file_exists(Allconfig::$adsImgPaths['thumb'].$v['img'])){
                                unlink(Allconfig::$adsImgPaths['thumb'].$v['img']);
                    }
                 $id_ads_array[] = $v['id_add'];
               };
              
               K_q::query("DELETE FROM gallery WHERE id_add IN (".implode(',',$id_ads_array).")");     
			   K_q::query("DELETE FROM ads WHERE id IN (".implode(',',$id_array).")");
			   
			   $countdelete = count($id_array);
			   
			   return $countdelete;
			   
     }   
	 
    public function genImages($imgPath, $newName){

					require_once LIB_PATH."/img_tool_kit/AcImage.php";
					
					try {
						$image = AcImage::createImage($imgPath);
					} catch (Exception $e) {
						echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
						return false;
					}
					
					if($image){
					
						try {
							$image->saveAsJPG(AllConfig::$adsImgPaths['original'].$newName);
						  
							if(file_exists($imgPath)){
								unlink($imgPath);
							}
							
							//  накладываем лого	
							$image->resize(1360, 768)->drawLogo(AllConfig::$adsImgPaths['watermarkImport'],2,10)->save(AllConfig::$adsImgPaths['big'].$newName);	
							
							if($image = AcImage::createImage(AllConfig::$adsImgPaths['big'].$newName)){
								
								$image->simpleResize(180, 135)->save(AllConfig::$adsImgPaths['thumb'].$newName);
							
							}
							
              			}catch(Exception $e){
						
							echo 'Выброшено исключение: ',  $e->getMessage(), ", удалены картинки\n";
							
							if(file_exists(AllConfig::$adsImgPaths['thumb'].$newName)){
							
								unlink(AllConfig::$adsImgPaths['thumb'].$newName);
								
							}
								
							if(file_exists(AllConfig::$adsImgPaths['thumb'].$newName)){
							
								unlink(AllConfig::$adsImgPaths['big'].$newName);
								
							}							
							
							return false;
						}
						
					}else{
						return false;
					}
    }
	
	public function add($data, $import = false){
	
	
		$this->init();
	
	$validate = array(
                                  
                          'category' => array( 'required' => true, 'enum'=>$this->category ),
                          
                          'region' => array( 'required' => true, 'enum'=>$this->region ),
                                                    
                          'proposal' => array( 'required' => true, 'enum'=>$this->proposal ),
						  
						//  'text' => array( 'required' => true, 'minlen' => 50, 'maxlen' => 2000 ),
						  
						  'zone2' => array( 'required' => false )
						  
                                                    
				        );
						
						
						if($data['type_transac'] == 87 || $data['type_transac'] == 88) {$validate['type_transac_dop']['required'] = true; $validate['type_transac_dop']['enum'] = $this->type_transac_dop;} else {$validate['type_transac_dop']['required'] = false; unset($validate['type_transac_dop']['enum']);}
		if ($data['type_transac_dop'] == 84 || $data['type_transac_dop'] == 85 || $data['type_transac_dop'] == 89 || $data['category'] == 8 || $data['category'] == 5 || $data['type_propert'] == 16 || $data['type_propert'] == 18) {
		
		
		$validate['street']['required'] = false;
		//$validate['house']['required'] = false;
		$validate['rooms']['required'] = false;
		$validate['walls']['required'] = false;
		$validate['state']['required'] = false;
		$validate['fla']['required'] = false;
		$validate['flb']['required'] = false;
		
		unset($validate['walls']['enum']);
		unset($validate['state']['enum']);
		unset($validate['fla']['enum']);
		unset($validate['flb']['enum']);
		
			/*$enumwalls = '';
			$enumstate = '';
			$enumfla = '';
			$enumflb = '';*/
		
		}
		else {
		
		$validate['street']['required'] = true;
		//$validate['house']['required'] = false;
		$validate['rooms']['required'] = true;
		$validate['walls']['required'] = true;
		$validate['state']['required'] = true;
		$validate['fla']['required'] = true;
		$validate['flb']['required'] = true;
		
		$validate['walls']['enum'] = $this->walls;
		$validate['state']['enum'] = $this->state;
		$validate['fla']['enum'] = $this->fla;
		$validate['flb']['enum'] = $this->flb;
		
			/*$enumwalls = ", 'enum'=>$this->walls";
			$enumstate = ", 'enum'=>$this->state";
			$enumfla = ", 'enum'=>$this->fla";
			$enumflb = ", 'enum'=>$this->flb";*/
		
		}
		if ($data['category'] == 5 || $data['type_transac_dop'] == 92 || $data['type_transac_dop'] == 85 || $data['type_transac_dop'] == 84) {$validate['area1']['required'] = false;} else {$validate['area1']['required'] = true;}
		if ($data['type_transac_dop'] !== 1) {$validate['obmen']['required'] = false; unset($validate['obmen']['enum']);} else {$validate['obmen']['required'] = true; $validate['obmen']['enum'] = $this->obmen;}
		if ($data['category'] != 1) {$validate['fla']['required'] = false; $validate['flb']['required'] = false;}
		
	
		if ((isset($data['zone2']))&&(!empty($data['zone2']))) {
			$validate['obmen']['required'] = array_keys($this->zone2, $data['city']);
		}
		
		if ($data['category'] == 13) {
			
			$validate['type_propert']['required'] = false;
			unset($validate['type_propert']['enum']);
		
		} 
		else {
		
			$validate['type_propert']['required'] = true;
			$validate['type_propert']['enum'] = array_keys($this->type_propert, $data['category']);
		
		}

		if (isset($data['dogov'])&&($data['dogov']!=0)) {

			$validate['street']['required'] = false;
	
		}
		else {$validate['street']['required'] = true;}
		
		if (((isset($data['stock_f']))&&(isset($data['stock_l'])))&&(($data['stock_l']==1)||($data['stock_f']==1))) {

			$validate['fla']['required'] = false;
			$validate['flb']['required'] = false;
	
		}
		
		if (($validate['fla']['required']==true)||($validate['flb']['required']==true)) {
			$validate = array('fla' => array ( 'max' => 30, 'min' => 1, 'int'));
		}
		
		if($data['type_transac'] == 92) {$validate['street']['required'] = false;$validate['obmen']['required'] = false;} 
		
		$enddate = strtotime(date("Y-m-d").' +90 day');
		$data['enddate'] = date('Y-m-d', $enddate);
		
		if ($import == true) {
			//$validate['house']['required'] = false;
			$validate['area1']['required'] = false;
			$validate['type_propert']['required'] = false;
			$validate['rooms']['required'] = false;
			$validate['walls']['required'] = false;
			$validate['street']['required'] = false;
			$validate['obmen']['required'] = false;
			$validate['state']['required'] = false;
		}
		
		if($data['type_transac'] == 88) {$validate['obmen']['required'] = false;} 

		//var_dump($validate['category']);
		$this->fieldsNames = array(
					
					'type_transac_dop' => t('Тип объявления:','Тип об&#39;яви:'),
                    'category' => t('Категория:','Категорія:'),
					'region' => t('Область:','Область:'),
					'zone' => t('Населённый пункт:','Населений пункт:'),
					'city' => t('Город:','Місто:'),
					'zone2' => t('Район:','Район:'),
                    'street' => t('Улица:','Вулиця:'),
                    'house' => t('Дом:','Дім:'), 
					'rooms' => t('Комнат:','Кімнат:'),
					'walls' => t('Стены:','Стіни:'),
					'state' => t('Состояние:','Стан:'),
					'area1' => t('Общая площадь:','Загальна площа:'),
					'fla' => t('Этаж:','Поверх:'),
					'flb' => t('Всего этажей:','Усього поверхів:'),
					'price' => t('Цена:','Ціна:'),
					'obmen' => t('Обмен:','Обмін:'),
					'proposal' => t('Предложение:','Пропозиція:'),		
                    'text' => t('Описание:','Опис:'),
					'type_propert' => t('Тип недвижимости:','Тип нерухомості:'),
					'saveerror' => t('Запись в базу:','Додаток до бази')
                                        
			    );
	
			$validate['zone'] = array( 'required' => false/*, 'enum'=>array_keys($this->zone, $data['region']) */);
			$validate['city'] = array( 'required'/*, 'enum'=>array_keys($this->city, $data['zone'])*/ );
									
			$dictionary = new K_Dictionary;
			
			$dictionary->loadFromIni(CONFIGS_PATH.'/forms/ads.txt');
			//var_dump($validate);
			
			$this->setDictionary($dictionary);
						//var_dump($data['category']);
						
						//var_dump($this->isValidRow($data, $validate));
						
			if ($this->isValidRow($data, $validate)) {
					//var_dump($data);
				//var_dump($this->save($data));
				if(!$this->save($data)) {
					$this->setError( "saveerror", 'SAVE_ERROR', array($test ) );
					return false;
				}
				else {
									
					return true;
					
				}
				
			}			
			else {
				return false;
			}
				
    }
		
	public function fromCity($id) {
	
		$q = k_q::row("select * from city where id=".$id."");
		
		if ($q['zone2'] == 1) return true; //областной центр
		else return false; //населёный пункт
	
	}
	
	static public function get() {
	
		if(self::$inst) {
		
			return self::$inst;
		
		}
		else {
		
     		self::$inst = new Site_Model_Ads;
			return self::$inst;
			
		}
	
	}
      
}

?>