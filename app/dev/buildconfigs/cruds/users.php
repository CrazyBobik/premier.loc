 <?php
 // создаём контроллер нашего круда 
        // создаём контроллер нашего круда 
          $crudConfig = array(                                  'name'=> "users",
                                                                'table'=> "users",
                                                                'title'=> "Редактирование пользователей",
                                                                'alias' =>'u',
                                                                'primary' =>'id',
                                                                'fieldsMaxLen' =>'64',
                                                                'model' =>'Site_Model_User',
                                                                'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                $where ORDER BY id ASC LIMIT $start, $onPage',
                                                                
                                                                'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                WHERE <%prefixid%> = $id',
                                                                                
                                                                'fields'=>array( 'id'=> array(  'width'=>'50',
                                                                                                'lable'=>'ID',
                                                                                                'set'=>"like",
                                                                                                'type'=>"int"
                                                                                              ),
                                                                                  'mail'=> array( 'width'=>'120',
                                                                                                  'lable'=>"Email",
                                                                                                  'set'=>"like",
                                                                                                  'validate'=>array(
                                                                                                         'required' => true,
                                                                                                         'notEmpty',
                                                                                                         'email',
                                                                									  	 'userExists'
                                                                                                  )
                                                                                                 ),
                                                                                  'name'=> array( 'width'=>'120',
                                                                                                  'lable'=>"Имя",
                                                                                                  'set'=>"like",
                                                                                                  'validate'=>array(
                                                                                                         'required' => true,
                                                                                                         'notEmpty',
                                                                                                         'minlen'=>3,
                                                                                                         'maxlen'=>64,
                                                                                                  )
                                                                                                ),
                                                                                  'fam'=> array( 'width'=>'120',
                                                                                                 'set'=>"like",
                                                                                                 'lable'=>'Фамилия',
                                                                                                 'validate'=>array(
                                                                                                         'required' => true,
                                                                                                         'notEmpty',
                                                                                                         'minlen'=>3,
                                                                                                         'maxlen'=>64,
                                                                                                  )
                                                                                                
                                                                                              ),
                                                                                   'colpub'=> array( 'width'=>'50',
                                                                                                    'lable'=>'Доступно публикаций',
                                                                                                    'set'=>"between",
                                                                                                    'validate'=>array(
                                                                                                         'int'
                                                                                                     )
                                                                                                  ),                  
                                                                                  'colpub_all'=> array( 'width'=>'50',
                                                                                                    'lable'=>'Всего публикаци',
                                                                                                    'set'=>"between",
                                                                                                    'validate'=>array(
                                                                                                        'int'
                                                                                                     )
                                                                                                  ),            
                                                                                  'balans'=> array( 'width'=>'50',
                                                                                                    'lable'=>'Баланс',
                                                                                                    'set'=>"between",
                                                                                                    'validate'=>array(
                                                                                                         'int'
                                                                                                     )
                                                                                                  ),
                                                                                  'pkt'=> array( 'width'=>'120',
                                                                                                 'lable'=>'Пакет',
                                                                                                 'alias'=>'p',
                                                                                                 'field'=>'title',
                                                                                                 'set'=>"add",
                                                                                                 'otions'=>array(
                                                                                                            'table'=>'pkt',
                                                                                                            'value'=>'id',
                                                                                                            'title'=>'title'
                                                                                                 ),
                                                                                                 'validate'=>array(
                                                                                                         'int'
                                                                                                 )
                                                                                               ),
                                                                                  'avatar'=> array( 'width'=>'100',
                                                                                                    'lable'=>'Аватар',
                                                                                                    'template'=>'<img height="40" src="/img/avatars/\'.<%value%>.\'"/>'
                                                                                                   ),
                                                                                  'date'=> array( 'width'=>'120',
                                                                                                  'lable'=>'Дата регистрации',
                                                                                                  'type'=>"TIMESTAMP",
                                                                                                  'set'=>"between",
                                                                                                  )  
                                                               )   
        );