 <?php
 // создаём контроллер нашего круда 
          $crudConfig = array(                                  'name'=> "ads",
                                                                'title'=> "Объявления",
                                                                'table'=> "ads",
                                                                'alias' =>'a',
                                                                'primary' =>'id',
                                                                'fieldsMaxLen' =>'10000',
                                                                'model' =>'Site_Model_Ads',
                                                                
                                                                'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                                                                
                                                                                     LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN transac t ON t.id=<%alias%>.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=<%alias%>.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=<%alias%>.type_propert
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                    
                                                                                $where ORDER BY id ASC LIMIT $start, $onPage',
                                                                
                                                                'editQuery' => 'SELECT <%fields%>  FROM `<%table%>` <%alias%>
                                                                
                                                                                     LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN transac t ON t.id=<%alias%>.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=<%alias%>.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=<%alias%>.type_propert
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                     
                                                                                WHERE <%prefixid%> = $id',
                                                                                
                                                                'fields'=>array( 'id'=> array(  'width'=>'40',
                                                                                                'lable'=>'ID',
                                                                                                'set'=>"like",
                                                                                                'type'=>"int"
                                                                                              ),
                                                                                              
                                                                                 'date'=> array( 'width'=>'80',
                                                                                                  'lable'=>"Дата в архив",
                                                                                                  'set'=>"between",
                                                                                                  'type'=>"TIMESTAMP"
                                                                                                 ),
                                                                                                 
                                                                                 'pub'=> array(  'width'=>'80',
                                                                                                  'lable'=>"Дата публикации",
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
                                                                                                  )
                                                                                                
                                                                                              ),
                                                                                              
                                                                                 'type_transac'=> array('width'=>'80',
                                                                                                         'set'=>"add",
                                                                                                         'lable'=>'Операция',
                                                                                                         'alias'=>'t',
                                                                                                         'field'=>'title',
                                                                                                         'otions'=>array(
                                                                                                                    'table'=>'transac',
                                                                                                                    'value'=>'id',
                                                                                                                    'title'=>'title'
                                                                                                         ),
                                                                                                         'validate'=>array(
                                                                                                                     'int'
                                                                                                         )
                                                                                                 ),
                                                                                                              
                                                                                  'category'=> array( 'width'=>'80',
                                                                                                      'lable'=>'Категория',
                                                                                                      'set'=>"add",
                                                                                                      'alias'=>'c',
                                                                                                      'field'=>'title',
                                                                                                      'otions'=>array(
                                                                                                                'table'=>'ads_sec',
                                                                                                                'value'=>'id',
                                                                                                                'title'=>'title'
                                                                                                      ),
                                                                                                      'validate'=>array(
                                                                                                                 'int'
                                                                                                      )
                                                                                                  ),
                                                                                                  
                                                                                  'type_propert'=> array( 'width'=>'80',
                                                                                                         'lable'=>'Раздел',
                                                                                                         'set'=>"add",
                                                                                                         'alias'=>'p',
                                                                                                         'field'=>'title',
                                                                                                         'otions'=>array(
                                                                                                                'table'=>'ads_subsec',
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
                                                                                                  )
                                                              )   
        );