 <?php
 // создаём контроллер нашего круда 
          $crudConfig = array(                                  'name'=> "services",
                                                                'title'=> "Компании",
                                                                'table'=> "services_cont",
                                                                'alias' =>'s',
                                                                'primary' =>'id',
                                                                'fieldsMaxLen' =>'10000',
                                                                'model' =>'Site_Model_Service',
                                                                
                                                                'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` `<%alias%>`
                                                                                
																					 LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                     LEFT JOIN services t ON t.id=<%alias%>.type
                                                                                     LEFT JOIN services_list l ON l.id=<%alias%>.category
                                                                                
                                                                                $where ORDER BY <%alias%>.id DESC LIMIT $start, $onPage',
                                                                
                                                                'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` `<%alias%>`
                                                                                
																					 LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                     LEFT JOIN services t ON t.id=<%alias%>.type
                                                                                     LEFT JOIN services_list l ON l.id=<%alias%>.category
                                                                                                                                                                
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
																		                                                                                                  
                                                                                  'category'=> array('width'=>'80',
                                                                                                         'lable'=>'Раздел',
                                                                                                         'set'=>"add",
                                                                                                         'alias'=>'l',
                                                                                                         'field'=>'title',
                                                                                                         'otions'=>array(
                                                                                                                'table'=>'services_list',
                                                                                                                'value'=>'id',
                                                                                                                'title'=>'title'
                                                                                                         ),
                                                                                                         'validate'=>array(
                                                                                                                'int'
                                                                                                         )
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
                                                                             
                                                                                  'logo'=> array( 'width'=>'80',
                                                                                                    'lable'=>'Лого',
                                                                                                    'template'=>'<img width="70" src="/img/services/thumb/\'.<%value%>.\'"/>'
                                                                                                ),
                                                                         		  'moderation'=> array( 'width'=>'25',
																							  'lable'=>"Утв..",
																							  'set'=>"add",
																							  'validate'=>array(
																									 'int' => true,
																								  )
																							),	
																				  'title'=> array( 'width'=>'50',
																						  'lable'=>"Заголовок.",
																						  'set'=>"add"
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
																						)
													            )
							);
?>