 <?php
 // создаём контроллер нашего круда 
          $crudConfig = array(                                  'name'=> "jk",
                                                                'title'=> "Новостройки ЖК",
                                                                'table'=> "novostroyki",
                                                                'alias' =>'n',
                                                                'primary' =>'id',
                                                                'fieldsMaxLen' =>'10000',
                                                                'model' =>'Site_Model_Jk',
                                                                
                                                                'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` `<%alias%>`
                                                                                
																					 LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                                                                                               
                                                                                $where ORDER BY <%alias%>.id DESC LIMIT $start, $onPage',
                                                                
                                                                'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` `<%alias%>`
                                                                                
																					 LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                                                                                                                                                                                 
                                                                                WHERE <%prefixid%> = $id',
                                                                                
                                                                'fields'=>array( 'id'=> array(  'width'=>'25',
                                                                                                'lable'=>'ID',
                                                                                                'set'=>"like",
                                                                                                'type'=>"int"																								
                                                                                              ),
																				  'date'=> array( 'width'=>'80',
																							  'lable'=>"Дата добавления",
																							  'set'=>"between",
																							  'type'=>"TIMESTAMP"
																							 ),			  
                                                                              
																				  'user'=> array( 'width'=>'100',
																						  'set'=>"add",
																						  'lable'=>'Пользователь',
																						  'alias'=>'u',
																						  'field'=>'mail',
																						  'otions'=>array(
																									'table'=>'users',
																									'value'=>'id',
																									'title'=>'mail'
																						  ),
																						  'validate'=>array(
																									 'int'
																						  ),
																						  'template'=>'<a href="//\'.<%value%>.\'"/>\'.<%value%>.\'</a>'
                                                                               
																					    ),
																		                          
                                                                                  'region'=> array( 'width'=>'80',
                                                                                                         'lable'=>'Область',
                                                                                                         'set'=>"add",
                                                                                                         'alias'=>'r',
                                                                                                         'field'=>'name',
                                                                                                         'otions'=>array(
                                                                                                                'table'=>'region',
                                                                                                                'value'=>'id',
                                                                                                                'title'=>'name'
                                                                                                         ),
                                                                                                         'validate'=>array(
                                                                                                                'int'
                                                                                                         )
                                                                                                  ),		
																					'jk_name'=> array( 'width'=>'50',
																						  'lable'=>"Наз. жк",
																						  'set'=>"like"
																						),	
                                                                                    'img'=> array('width'=>'80',
                                                                                                    'lable'=>'Фото жк',
                                                                                                    'template'=>'<img width="70" src="/img/novostroyki/\'.<%value%>.\'"/>'
                                                                                                ),
																					'company_name'=> array('width'=>'50',
																						  'lable'=>"Наз. компании",
																						  'set'=>"like"
																						),	
                                                                                    'company_logo'=> array('width'=>'80',
                                                                                                    'lable'=>'Лого компании',
                                                                                                    'template'=>'<img width="70" src="/img/companieslogos/thumb/\'.<%value%>.\'.jpg"/>'
                                                                                                ),
																				    'moderation'=> array( 'width'=>'25',
																							  'lable'=>"Утв..",
																							  'set'=>"add",
																							  'validate'=>array(
																									 'int' => true,
																								  )
																							),	
																				    'site'=> array( 'width'=>'50',
																						  'lable'=>"Сайт",
																						  'set'=>"like"
																						),		
																				    'text'=> array('width'=>'150',
																						  'lable'=>"Текст",
																						  'set'=>"like",
																						  'crop'=>150,
																						  'type'=>"text"
																						),
																					'street'=> array(
																						  'lable'=>"Адрес",
																						 
																						),
																					'house'=> array(
																						  'lable'=>"Номер дома",
																						),
																					'sec_num'=> array(
																						  'lable'=>"Количество секций",
																						  
																						),
																					'sec_level'=> array(
																						  'lable'=>"Количество секций"
																						),
																					'flat_num'=> array(
																						  'lable'=>"Количество квартир"
																						),
																					'parking'=> array(
																						  'lable'=>"Количество квартир"
																						),
																					'material'=> array(
																						  'lable'=>"Материал"
																						),
																					'code'=> array(
																						  'lable'=>"Код телефона"
																						),
																					'phone'=> array(
																						  'lable'=>"Телефон"
																						),
																					'email'=> array(
																						  'lable'=>"Электронная почта"
																						),
																					'start_date'=> array(
																						  'type'=>"TIMESTAMP",
																						  'lable'=>"Дата начала строительства"
																						),
																					'finish_date'=> array(
																						  'type'=>"TIMESTAMP",
																						  'lable'=>"Дата сдачи"
																						),
																					'price_from'=> array(
																						  'lable'=>"Цена от"
																						),
																					'price_to'=> array(
																						  'lable'=>"Цена до"
																						),
																					'site'=> array(
																						  'lable'=>"Сайт"
																						),
																					'video_link'=> array(
																						  'lable'=>"Видео на youtube"
																						),
																					'is_complete'=> array( 
																						  'lable'=>"Объект сдан?"
																						)
																			)
							);
?>