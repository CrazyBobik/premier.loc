 <?php
 // создаём контроллер нашего круда 
 $crudConfig = array(
     'name'=> "objects",
     'title'=> "Обекты",
     'table'=> "objects",
     'alias' =>'a',
     'primary' =>'id',
     'fieldsMaxLen' =>'10000',
     'model' =>'Site_Model_Objects',
                                                                
     'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>

                      LEFT JOIN type_country cunt ON cunt.type_country_id=<%alias%>.country
                      LEFT JOIN type_region r ON r.type_region_id=<%alias%>.region
                      LEFT JOIN type_city ci ON ci.type_city_id=<%alias%>.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=<%alias%>.type
                      $where ORDER BY id ASC LIMIT $start, $onPage',
                                                                
     'editQuery' => 'SELECT <%fields%>  FROM `<%table%>` <%alias%>
                                                                
                                                                                     LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN transac t ON t.id=<%alias%>.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=<%alias%>.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=<%alias%>.type_propert
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                     
                                                                                WHERE <%prefixid%> = $id',
                                                                                
     'fields'=>array(
         'id'=> array(
         'width'=>'40',
         'lable'=>'ID',
         'set'=>"like",
         'type'=>"int"
     ),

         'country'=> array(
             'width'=>'80',
             'set'=>"add",
             'lable'=>'Страна',
             'alias'=>'cunt',
             'field'=>'type_country_name',
             'otions'=>array(
                 'table'=>'type_country',
                 'value'=>'type_country_id',
                 'title'=>'type_country_name'
             ),
             'validate'=>array(
                 'int'
             )
         ),

         'category'=> array(
             'width'=>'80',
             'lable'=>'Регион',
             'set'=>"add",
             'alias'=>'r',
             'field'=>'type_region_name',
             'otions'=>array(
                 'table'=>'type_region',
                 'value'=>'type_region_id',
                 'title'=>'type_region_name'
             ),
             'validate'=>array(
                 'int'
             )
         ),

         'type'=> array(
             'width'=>'80',
             'lable'=>'Тип',
             'set'=>"add",
             'alias'=>'jk',
             'field'=>'type_typejk_id',
             'otions'=>array(
                 'table'=>'type_typejk',
                 'value'=>'type_typejk_id',
                 'title'=>'type_typejk_name'
             ),
             'validate'=>array(
                 'int'
             )
         ),

         'area'=> array(
             'width'=>'40',
             'lable'=>'площадь',
             'set'=>"like",
             'type'=>"int"
 ),

     )
 );