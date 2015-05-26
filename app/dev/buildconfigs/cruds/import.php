 <?php
 // создаём контроллер нашего круда 
 $crudConfig = array(
     'name'=> "import",
     'title'=> "Выгрузка",
     'table'=> "import",
     'alias' =>'imp',
     'primary' =>'id',
     'fieldsMaxLen' =>'10000',
     'model' =>'Site_Model_Import',
                                                                
     'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                      $where ORDER BY id ASC LIMIT $start, $onPage',
                                                                
     'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                      WHERE <%prefixid%> = $id',
                                                                                
     'fields'=>array(
         'id'=> array(
             'width'=>'30',
             'lable'=>'ID',
             'set'=>"like",
             'type'=>"int"
         ),

//         'country'=> array(
//             'width'=>'70',
//             'set'=>"add",
//             'lable'=>'Страна',
//             'alias'=>'cunt',
//             'field'=>'type_country_name',
//             'otions'=>array(
//                 'table'=>'type_country',
//                 'value'=>'type_country_id',
//                 'title'=>'type_country_name'
//             ),
//             'validate'=>array(
//                 'int'
//             )
//         ),

         'start_date'=> array(
             'width'=>'80',
             'lable'=>'Запускался',
             'set'=>"between",
             'type'=>"TIMESTAMP"
         ),

         'infeed'=> array(
             'width'=>'80',
             'lable'=>'В файле импорта',
             'set'=>"between",
             'type'=>"int"
         ),

         'processed'=> array(
             'width'=>'80',
             'lable'=>'Обработ.',
             'set'=>"between",
             'type'=>"int"
         ),

         'publicated'=> array(
             'width'=>'80',
             'lable'=>'Опубликован',
             'set'=>"between",
             'type'=>"int"
         ),

         'updated'=> array(
             'width'=>'80',
             'lable'=>'Обновлен',
             'set'=>"between",
             'type'=>"int"
         ),

         'deleted'=> array(
             'width'=>'80',
             'lable'=>'Удален',
             'set'=>"between",
             'type'=>"int"
         ),

         'errors'=> array(
             'width'=>'80',
             'lable'=>'Ошибок',
             'set'=>"between",
             'type'=>"int"
         ),

         'add_date'=> array(
             'width'=>'80',
             'lable'=>'Добавлен',
             'set'=>"between",
             'type'=>"TIMESTAMP"
         ),

         'limit'=> array(
             'width'=>'80',
             'lable'=>'Лимит',
             'set'=>"between",
             'type'=>"int"
         )

     )
 );