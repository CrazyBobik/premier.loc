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
                      LEFT JOIN market m ON m.id=<%alias%>.market
                      LEFT JOIN currency cu ON cu.id=<%alias%>.cur
                      LEFT JOIN state s ON s.id=<%alias%>.state
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
         'width'=>'30',
         'lable'=>'ID',
         'set'=>"like",
         'type'=>"int"
     ),

         'country'=> array(
             'width'=>'70',
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

         'region'=> array(
             'width'=>'70',
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

         'city'=> array(
             'width'=>'70',
             'lable'=>'Город',
             'set'=>"add",
             'alias'=>'ci',
             'field'=>'type_city_name',
             'otions'=>array(
                 'table'=>'type_city',
                 'value'=>'type_city_id',
                 'title'=>'type_city_name'
             ),
             'validate'=>array(
                 'int'
             )
         ),

         'market'=> array(
             'width'=>'50',
             'lable'=>'Рынок',
             'set'=>"add",
             'alias'=>'m',
             'field'=>'name',
             'otions'=>array(
                 'table'=>'market',
                 'value'=>'id',
                 'title'=>'name'
             ),
             'validate'=>array(
                 'int'
             )
         ),

         'type'=> array(
             'width'=>'60',
             'lable'=>'Тип',
             'set'=>"add",
             'alias'=>'jk',
             'field'=>'type_typejk_name',
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
             'width'=>'30',
             'lable'=>'Пл.',
             'set'=>"like",
             'type'=>"int"
         ),

         'all_sq'=> array(
             'width'=>'30',
             'lable'=>'вся жилая пл.',
             'set'=>"like",
             'type'=>"int"
         ),

         'living_sq'=> array(
             'width'=>'30',
             'lable'=>'жилая пл.',
             'set'=>"like",
             'type'=>"int"
         ),

         'kithcen_sq'=> array(
             'width'=>'30',
             'lable'=>'пл. кухни',
             'set'=>"like",
             'type'=>"int"
         ),

         'price'=> array(
             'width'=>'40',
             'lable'=>'цена',
             'set'=>"like",
             'type'=>"int"
         ),

         'cur'=> array(
             'width'=>'50',
             'lable'=>'Валюта',
             'set'=>"add",
             'alias'=>'cu',
             'field'=>'name',
             'otions'=>array(
                 'table'=>'currency',
                 'value'=>'id',
                 'title'=>'name'
             ),
             'validate'=>array(
                 'int'
             )
         ),

         'to_sea'=> array(
             'width'=>'30',
             'lable'=>'раст. до моря',
             'set'=>"like",
             'type'=>"int"
         ),

         'to_airport'=> array(
             'width'=>'30',
             'lable'=>'раст. до аероп.',
             'set'=>"like",
             'type'=>"int"
         ),

         'rooms'=> array(
             'width'=>'30',
             'lable'=>'кол. ком.',
             'set'=>"like",
             'type'=>"int"
         ),

         'floor'=> array(
             'width'=>'30',
             'lable'=>'этаж',
             'set'=>"like",
             'type'=>"int"
         ),

         'all_floors'=> array(
             'width'=>'30',
             'lable'=>'кол. этажей',
             'set'=>"like",
             'type'=>"int"
         ),

         'bath_rooms'=> array(
             'width'=>'30',
             'lable'=>'кол. ваных ком.',
             'set'=>"like",
             'type'=>"int"
         ),

         'state'=> array(
             'width'=>'70',
             'lable'=>'Состояние',
             'set'=>"add",
             'alias'=>'s',
             'field'=>'name',
             'otions'=>array(
                 'table'=>'state',
                 'value'=>'id',
                 'title'=>'name'
             ),
             'validate'=>array(
                 'int'
             )
         )

     )
 );