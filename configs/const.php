<?php
class constant{
     
        static public $catalog = array(
		
                        '1' => array('title'=>'Квартиры',
									 'sub'=>array('24'=>array('title'=>'Квартира'),
												  '26'=>array('title'=>'Квартира в новострое'),
												  '25'=>array('title'=>'Комната')
												 )												 
						
						);
						'3' => array('title'=>'Дома',
									 'sub'=>array('1'=>array('title'=>'Дом'),
												  '2'=>array('title'=>'Часть дома'),
												  '3'=>array('title'=>'Дача'),
												  '27'=>array('title'=>'Котедж')
												  )
						
						);
						'5' => array('title'=>'Гаражи',
									 'sub'=>array('4'=>array('title'=>'Место в гаражном комплексе'),
												  '5'=>array('title'=>'Подземный паркинг'),
												  '6'=>array('title'=>'Отдельно стоящий гараж'),
												  '7'=>array('title'=>'Место на стоянке')
												  )
						
						);
						'6' => array('title'=>'Коммерческая недвижимость',
									 'sub'=>array('13'=>array('title'=>'Под офис'),
												  '14'=>array('title'=>'Офисное помещение'),
												  '15'=>array('title'=>'Здание'),
												  '16'=>array('title'=>'Готовый бизнес'),
												  '17'=>array('title'=>'База отдыха'),
												  '18'=>array('title'=>'Отель, гостиница'),
												  '19'=>array('title'=>'Сфера услуг'),
												  '20'=>array('title'=>'Кафе, ресторан'),
												  '21'=>array('title'=>'Производственные посещения'),
												  '22'=>array('title'=>'Торговые площади'),
												  '23'=>array('title'=>'Складские помещения'),
												  )
												  
						);
						'8' => array('title'=>'Земельные участки',
									 'sub'=>array('12'=>array('title'=>'Участок под жилую застройку'),
												  '8'=>array('title'=>'Земля природно-заповедного назначения'),
												  '9'=>array('title'=>'Земля рекреационного назначения'),
												  '10'=>array('title'=>'Земля сельскохозяйственного назначения'),
												  '11'=>array('title'=>'Земля коммерческого назначения')
												  )
						
						);
						'13' => array('title'=>'Номер в гостинице',
									 'sub'=>array('28'=>array('title'=>'Номер в отеле'),
												  '29'=>array('title'=>'Апартаменты')
												  )
						
						);
						
		);
		
		static public $dopType = array(
		
                        '1' => array('dop_walls','dop_val2','dop_val4','dop_val5','dop_state','dop_proposal','dop_obmen_b');											 
						
						'3' => array('dop_walls','dop_val2','dop_val4','dop_val5','dop_state','dop_proposal','dop_val8','dop_val9','dop_val10','dop_obmen_b');
						
						'5' => array('dop_nazn','dop_obmen_b');
						
						'6' => array('dop_val4_kom','dop_tob','dop_proposal','dop_obmen_b');
						
						'8' => array('dop_proposal','dop_obmen_b');
						
						'13' => array('dop_obmen_b');
						
		);
		
		static public $dopSubtype = array(
		
                        '2' => array('dop_walls','dop_val2','dop_val4','dop_val5','dop_state','dop_proposal','dop_val8');
						
		);
                    
        static public $mysqlDump = array(
       
                        'link'=>'testdump',
                        'secureTokenArg'=>'token', 
                        'secureToken'=>'elinokoll786', 
                        'insertRecordsCount'=>50
			           
					 ); 
                     
               
 
}