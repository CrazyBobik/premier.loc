<?php
$crudTables = array (
  'users' => 
  array (
    'name' => 'users',
    'table' => 'users',
    'title' => 'Редактирование пользователей',
    'alias' => 'u',
    'primary' => 'id',
    'fieldsMaxLen' => '64',
    'model' => 'Site_Model_User',
    'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                $where ORDER BY id ASC LIMIT $start, $onPage',
    'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                WHERE <%prefixid%> = $id',
    'fields' => 
    array (
      'id' => 
      array (
        'width' => '50',
        'lable' => 'ID',
        'set' => 'like',
        'type' => 'int',
      ),
      'mail' => 
      array (
        'width' => '120',
        'lable' => 'Email',
        'set' => 'like',
        'validate' => 
        array (
          'required' => true,
          0 => 'notEmpty',
          1 => 'email',
          2 => 'userExists',
        ),
      ),
      'name' => 
      array (
        'width' => '120',
        'lable' => 'Имя',
        'set' => 'like',
        'validate' => 
        array (
          'required' => true,
          0 => 'notEmpty',
          'minlen' => 3,
          'maxlen' => 64,
        ),
      ),
      'fam' => 
      array (
        'width' => '120',
        'set' => 'like',
        'lable' => 'Фамилия',
        'validate' => 
        array (
          'required' => true,
          0 => 'notEmpty',
          'minlen' => 3,
          'maxlen' => 64,
        ),
      ),
      'colpub' => 
      array (
        'width' => '50',
        'lable' => 'Доступно публикаций',
        'set' => 'between',
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'colpub_all' => 
      array (
        'width' => '50',
        'lable' => 'Всего публикаци',
        'set' => 'between',
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'balans' => 
      array (
        'width' => '50',
        'lable' => 'Баланс',
        'set' => 'between',
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'pkt' => 
      array (
        'width' => '120',
        'lable' => 'Пакет',
        'alias' => 'p',
        'field' => 'title',
        'set' => 'add',
        'otions' => 
        array (
          'table' => 'pkt',
          'value' => 'id',
          'title' => 'title',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'avatar' => 
      array (
        'width' => '100',
        'lable' => 'Аватар',
        'template' => '<img height="40" src="/img/avatars/\'.<%value%>.\'"/>',
      ),
      'date' => 
      array (
        'width' => '120',
        'lable' => 'Дата регистрации',
        'type' => 'TIMESTAMP',
        'set' => 'between',
      ),
    ),
  ),
  'ads' => 
  array (
    'name' => 'ads',
    'title' => 'Обекты',
    'table' => 'ads',
    'alias' => 'a',
    'primary' => 'id',
    'fieldsMaxLen' => '10000',
    'model' => 'Site_Model_Objects',
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
    'fields' => 
    array (
      'id' => 
      array (
        'width' => '40',
        'lable' => 'ID',
        'set' => 'like',
        'type' => 'int',
      ),
    ),
  ),
  'services' => 
  array (
    'name' => 'services',
    'title' => 'Компании',
    'table' => 'services_cont',
    'alias' => 's',
    'primary' => 'id',
    'fieldsMaxLen' => '10000',
    'model' => 'Site_Model_Service',
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
    'fields' => 
    array (
      'id' => 
      array (
        'width' => '25',
        'lable' => 'ID',
        'set' => 'like',
        'type' => 'int',
      ),
      'date' => 
      array (
        'width' => '80',
        'lable' => 'Дата добавления',
        'set' => 'between',
        'type' => 'TIMESTAMP',
      ),
      'user' => 
      array (
        'width' => '100',
        'set' => 'add',
        'lable' => 'Пользователь',
        'alias' => 'u',
        'field' => 'mail',
        'otions' => 
        array (
          'table' => 'users',
          'value' => 'id',
          'title' => 'mail',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
        'template' => '<a href="//\'.<%value%>.\'"/>\'.<%value%>.\'</a>',
      ),
      'category' => 
      array (
        'width' => '80',
        'lable' => 'Раздел',
        'set' => 'add',
        'alias' => 'l',
        'field' => 'title',
        'otions' => 
        array (
          'table' => 'services_list',
          'value' => 'id',
          'title' => 'title',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'region' => 
      array (
        'width' => '80',
        'lable' => 'Область',
        'set' => 'add',
        'alias' => 'r',
        'field' => 'name',
        'otions' => 
        array (
          'table' => 'region',
          'value' => 'id',
          'title' => 'name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'logo' => 
      array (
        'width' => '80',
        'lable' => 'Лого',
        'template' => '<img width="70" src="/img/services/thumb/\'.<%value%>.\'"/>',
      ),
      'moderation' => 
      array (
        'width' => '25',
        'lable' => 'Утв..',
        'set' => 'add',
        'validate' => 
        array (
          'int' => true,
        ),
      ),
      'title' => 
      array (
        'width' => '50',
        'lable' => 'Заголовок.',
        'set' => 'add',
      ),
      'site' => 
      array (
        'width' => '50',
        'lable' => 'Сайт',
        'set' => 'like',
      ),
      'text' => 
      array (
        'width' => '150',
        'lable' => 'Текст',
        'set' => 'like',
        'crop' => 150,
        'type' => 'text',
      ),
    ),
  ),
  'jk' => 
  array (
    'name' => 'jk',
    'title' => 'Новостройки ЖК',
    'table' => 'novostroyki',
    'alias' => 'n',
    'primary' => 'id',
    'fieldsMaxLen' => '10000',
    'model' => 'Site_Model_Jk',
    'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` `<%alias%>`
                                                                                
																					 LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                                                                                               
                                                                                $where ORDER BY <%alias%>.id DESC LIMIT $start, $onPage',
    'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` `<%alias%>`
                                                                                
																					 LEFT JOIN users u ON u.id=<%alias%>.user
                                                                                     LEFT JOIN region r ON r.id=<%alias%>.region
                                                                                                                                                                                                                                                 
                                                                                WHERE <%prefixid%> = $id',
    'fields' => 
    array (
      'id' => 
      array (
        'width' => '25',
        'lable' => 'ID',
        'set' => 'like',
        'type' => 'int',
      ),
      'date' => 
      array (
        'width' => '80',
        'lable' => 'Дата добавления',
        'set' => 'between',
        'type' => 'TIMESTAMP',
      ),
      'user' => 
      array (
        'width' => '100',
        'set' => 'add',
        'lable' => 'Пользователь',
        'alias' => 'u',
        'field' => 'mail',
        'otions' => 
        array (
          'table' => 'users',
          'value' => 'id',
          'title' => 'mail',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
        'template' => '<a href="//\'.<%value%>.\'"/>\'.<%value%>.\'</a>',
      ),
      'region' => 
      array (
        'width' => '80',
        'lable' => 'Область',
        'set' => 'add',
        'alias' => 'r',
        'field' => 'name',
        'otions' => 
        array (
          'table' => 'region',
          'value' => 'id',
          'title' => 'name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'jk_name' => 
      array (
        'width' => '50',
        'lable' => 'Наз. жк',
        'set' => 'like',
      ),
      'img' => 
      array (
        'width' => '80',
        'lable' => 'Фото жк',
        'template' => '<img width="70" src="/img/novostroyki/\'.<%value%>.\'"/>',
      ),
      'company_name' => 
      array (
        'width' => '50',
        'lable' => 'Наз. компании',
        'set' => 'like',
      ),
      'company_logo' => 
      array (
        'width' => '80',
        'lable' => 'Лого компании',
        'template' => '<img width="70" src="/img/companieslogos/thumb/\'.<%value%>.\'.jpg"/>',
      ),
      'moderation' => 
      array (
        'width' => '25',
        'lable' => 'Утв..',
        'set' => 'add',
        'validate' => 
        array (
          'int' => true,
        ),
      ),
      'site' => 
      array (
        'lable' => 'Сайт',
      ),
      'text' => 
      array (
        'width' => '150',
        'lable' => 'Текст',
        'set' => 'like',
        'crop' => 150,
        'type' => 'text',
      ),
      'street' => 
      array (
        'lable' => 'Адрес',
      ),
      'house' => 
      array (
        'lable' => 'Номер дома',
      ),
      'sec_num' => 
      array (
        'lable' => 'Количество секций',
      ),
      'sec_level' => 
      array (
        'lable' => 'Количество секций',
      ),
      'flat_num' => 
      array (
        'lable' => 'Количество квартир',
      ),
      'parking' => 
      array (
        'lable' => 'Количество квартир',
      ),
      'material' => 
      array (
        'lable' => 'Материал',
      ),
      'code' => 
      array (
        'lable' => 'Код телефона',
      ),
      'phone' => 
      array (
        'lable' => 'Телефон',
      ),
      'email' => 
      array (
        'lable' => 'Электронная почта',
      ),
      'start_date' => 
      array (
        'type' => 'TIMESTAMP',
        'lable' => 'Дата начала строительства',
      ),
      'finish_date' => 
      array (
        'type' => 'TIMESTAMP',
        'lable' => 'Дата сдачи',
      ),
      'price_from' => 
      array (
        'lable' => 'Цена от',
      ),
      'price_to' => 
      array (
        'lable' => 'Цена до',
      ),
      'video_link' => 
      array (
        'lable' => 'Видео на youtube',
      ),
      'is_complete' => 
      array (
        'lable' => 'Объект сдан?',
      ),
    ),
  ),
  'objects' => 
  array (
    'name' => 'objects',
    'title' => 'Обекты',
    'table' => 'objects',
    'alias' => 'a',
    'primary' => 'id',
    'fieldsMaxLen' => '10000',
    'model' => 'Site_Model_Objects',
    'loadQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                      LEFT JOIN type_country cunt ON cunt.type_country_id=<%alias%>.country
                      LEFT JOIN type_region r ON r.type_region_id=<%alias%>.region
                      LEFT JOIN type_city ci ON ci.type_city_id=<%alias%>.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=<%alias%>.type
                      LEFT JOIN market m ON m.id=<%alias%>.market
                      LEFT JOIN currency cu ON cu.id=<%alias%>.cur
                      LEFT JOIN state s ON s.id=<%alias%>.state
                      $where ORDER BY id ASC LIMIT $start, $onPage',
    'editQuery' => 'SELECT SQL_CALC_FOUND_ROWS <%fields%>  FROM `<%table%>` <%alias%>
                      LEFT JOIN type_country cunt ON cunt.type_country_id=<%alias%>.country
                      LEFT JOIN type_region r ON r.type_region_id=<%alias%>.region
                      LEFT JOIN type_city ci ON ci.type_city_id=<%alias%>.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=<%alias%>.type
                      LEFT JOIN market m ON m.id=<%alias%>.market
                      LEFT JOIN currency cu ON cu.id=<%alias%>.cur
                      LEFT JOIN state s ON s.id=<%alias%>.state
                      WHERE <%prefixid%> = $id',
    'fields' => 
    array (
      'id' => 
      array (
        'width' => '30',
        'lable' => 'ID',
        'set' => 'like',
        'type' => 'int',
      ),
      'country' => 
      array (
        'width' => '70',
        'set' => 'add',
        'lable' => 'Страна',
        'alias' => 'cunt',
        'field' => 'type_country_name',
        'otions' => 
        array (
          'table' => 'type_country',
          'value' => 'type_country_id',
          'title' => 'type_country_name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'region' => 
      array (
        'width' => '70',
        'lable' => 'Регион',
        'set' => 'add',
        'alias' => 'r',
        'field' => 'type_region_name',
        'otions' => 
        array (
          'table' => 'type_region',
          'value' => 'type_region_id',
          'title' => 'type_region_name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'city' => 
      array (
        'width' => '70',
        'lable' => 'Город',
        'set' => 'add',
        'alias' => 'ci',
        'field' => 'type_city_name',
        'otions' => 
        array (
          'table' => 'type_city',
          'value' => 'type_city_id',
          'title' => 'type_city_name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'market' => 
      array (
        'width' => '50',
        'lable' => 'Рынок',
        'set' => 'add',
        'alias' => 'm',
        'field' => 'name',
        'otions' => 
        array (
          'table' => 'market',
          'value' => 'id',
          'title' => 'name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'type' => 
      array (
        'width' => '60',
        'lable' => 'Тип',
        'set' => 'add',
        'alias' => 'jk',
        'field' => 'type_typejk_name',
        'otions' => 
        array (
          'table' => 'type_typejk',
          'value' => 'type_typejk_id',
          'title' => 'type_typejk_name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'area' => 
      array (
        'width' => '30',
        'lable' => 'Пл.',
        'set' => 'like',
        'type' => 'int',
      ),
      'all_sq' => 
      array (
        'width' => '30',
        'lable' => 'вся жилая пл.',
        'set' => 'like',
        'type' => 'int',
      ),
      'living_sq' => 
      array (
        'width' => '30',
        'lable' => 'жилая пл.',
        'set' => 'like',
        'type' => 'int',
      ),
      'kithcen_sq' => 
      array (
        'width' => '30',
        'lable' => 'пл. кухни',
        'set' => 'like',
        'type' => 'int',
      ),
      'price' => 
      array (
        'width' => '40',
        'lable' => 'цена',
        'set' => 'like',
        'type' => 'int',
      ),
      'cur' => 
      array (
        'width' => '50',
        'lable' => 'Валюта',
        'set' => 'add',
        'alias' => 'cu',
        'field' => 'name',
        'otions' => 
        array (
          'table' => 'currency',
          'value' => 'id',
          'title' => 'name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
      'to_sea' => 
      array (
        'width' => '30',
        'lable' => 'раст. до моря',
        'set' => 'like',
        'type' => 'int',
      ),
      'to_airport' => 
      array (
        'width' => '30',
        'lable' => 'раст. до аэроп.',
        'set' => 'like',
        'type' => 'int',
      ),
      'rooms' => 
      array (
        'width' => '30',
        'lable' => 'кол. ком.',
        'set' => 'like',
        'type' => 'int',
      ),
      'floor' => 
      array (
        'width' => '30',
        'lable' => 'этаж',
        'set' => 'like',
        'type' => 'int',
      ),
      'all_floors' => 
      array (
        'width' => '30',
        'lable' => 'кол. этажей',
        'set' => 'like',
        'type' => 'int',
      ),
      'bath_rooms' => 
      array (
        'width' => '30',
        'lable' => 'кол. ваных ком.',
        'set' => 'like',
        'type' => 'int',
      ),
      'state' => 
      array (
        'width' => '70',
        'lable' => 'Состояние',
        'set' => 'add',
        'alias' => 's',
        'field' => 'name',
        'otions' => 
        array (
          'table' => 'state',
          'value' => 'id',
          'title' => 'name',
        ),
        'validate' => 
        array (
          0 => 'int',
        ),
      ),
    ),
  ),
);