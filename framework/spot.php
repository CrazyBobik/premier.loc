<?php

// отдельный класс выделенный специально для функций данного сайта

class spot {
	
	public static function getObject($ind) {
		if (!$ind) {
			return false;
		}
		if (is_numeric($ind)) {
			$key = 'id';
			$notUrl = ' and url=""';
			// дополнительная проверка на пустой урл для этого обьекта, что-бы небыло повторов страниц
		} else {
			$key = 'url';
			$ind = trim($ind, '/');
			if (!$ind) {
				return false;
			}
		}

		//   $objectModel = new Admin_Model_Object;

		//   $object = $objectModel->mfr(select()->where(array($key => $ind)));
		// select *,(select name from obj_types where id=obj_objects.type limit 1) as typename, (select anchID from obj_objects  where id=".$_REQUEST['look']."   limit 1) as filial_info, (select symbol from obj_branches where id=obj_objects.branchID) as letter from obj_objects where id=".$_REQUEST['look']." limit 1"
		//    $db->setQuery('select id,title,text from obj_plans where objID='.$this->data['id'].'');
		// phone, address, email
		$q = new K_Query;

		$row = $q -> q("SELECT o.*, b.symbol letter, t.name typename, o.branchID filial_info, b.symbol letter, b.phone, b.address, b.email  FROM `obj_objects` o 
                        LEFT JOIN obj_rooms r ON r.id = o.id
                        LEFT JOIN obj_types t ON t.id = o.type
                        LEFT JOIN obj_branches b ON b.id = o.branchID 
                      WHERE o." . $key . "=" . K_Db_Quote::quote($ind) . ' ' . $notUrl, true);

		if ($row) {
			return $row[0];
		} else {
			return false;
		};
	}

	public static function seacrhe($query = array()) {
        $html = '';
        $arrReturn = array();

        $page = 1;
        $onPage = 10;
        if (isset($_GET['page'])){
            $page = (int) $_GET['page'];
        }

        $pag_info = K_Paginator::prepear($page, $onPage);

        $limit = " LIMIT ".$pag_info['start'].", ".$pag_info['onPage'];

        if (isset($query['id'])){
            $row = K_Q::row('SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name region,ci.type_city_name city,m.name market,jk.type_typejk_name type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,cu.name cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,s.name state,imf.img first_img FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      LEFT JOIN objects_img imf ON imf.id=a.first_img
                      WHERE a.id='.$query['id'].'GROUP BY a.id');
            ob_start();

            include(CHUNK_PATH.'/objlist.phtml');

            $html .= ob_get_contents();
            ob_end_clean();

            $arrReturn['html'] = $html;

            return $arrReturn;
        }
		
		$where = array();

        if (isset($query['country'])) {
            $where[] = 'a.country='.$query['country'];
        }
        if (isset($query['region'])) {
            $where[] = 'a.region='.$query['region'];
        }
        if (isset($query['city'])){
            $where[] = 'a.city='.$query['city'];
        }
        if (isset($query['type'])){
            $where[] = 'a.type='.$query['type'];
        }
        if (isset($query['market'])){
            $where[] = 'a.market='.$query['market'];
        }
        if (isset($query['sq'])){
            $where[] = 'a.area<='.$query['sq'];
        }
        if (isset($query['rooms'])){
            if ($query['rooms'] == 6) {
                $where[] = 'a.rooms>5';
            } else {
                $where[] = 'a.rooms='.$query['rooms'];
            }
        }
        if (isset($query['state'])){
            $where[] = 'a.state='.$query['state'];
        }
        if (isset($query['price_from'])){
            $where[] = 'a.price>='.$query['price_from'];
        }
        if (isset($query['price_to'])){
            $where[] = 'a.price<='.$query['price_to'];
        }

        if (count($where) > 0){
            $where = " WHERE ".implode(' AND ', $where);
        } else {
            $where = "";
        }

        $rows = K_Q::data('SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name region,ci.type_city_name city,m.name market,jk.type_typejk_name type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,cu.name cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,s.name state,imf.img first_img FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      LEFT JOIN objects_img imf ON imf.id=a.first_img'.$where.' GROUP BY a.id'.$limit);

        foreach($rows as $row){
            ob_start();

            include(CHUNK_PATH.'/objlist.phtml');

            $html .= ob_get_contents();
//     для рекламы       if ($k == 2) $html .= '<div style="margin: 5px auto; width: 720px;"><span data-link="http://zlkhome.com" class="jlinkn flink"><img src="/img/banners/34.jpg" width="720px" height="80px"></span></div>';
            ob_end_clean();

//     для рекламы       $k++;
        }
        $countItems = K_Q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
        $pages = ceil($countItems/$pag_info['onPage']);

        $arrReturn['html'] = $html;
        $arrReturn['page'] = $page;
        $arrReturn['pages'] = $pages;

		return $arrReturn;
	}	
	
	// функция генерирует маленькую картинку объекта для вывода на главной
	public static function minipictd($id, $street, $rooms, $price, $photos, $descr, $id1c, $filial_info, $filial_pref, $dtype, $status, $orient, $type, $url) {

		if ($url) {
			$object_url = '/' . $url;
		} else {
			$object_url = '/object-' . $id;
		}
        
		$orient = stripcslashes($orient);
		$returnHtml = '<td class="itembox">';
          
        $estPhoto = false;  
         
           
        for($i=0; $photos; $i++){
       
            if (file_exists(UPLOAD_PATH . "/objects/s" . $id . "_$i.jpg")) {
            
                    $m = $i;
                    $estPhoto = true; 
                    break;  
                    
    	    	}
            
        }
        
		if ($estPhoto){
			$returnHtml .= '<a href="' . $object_url . '"' . ($photos > 0 ? ' class="preview" id="m' . $id . '_0"' : '') . '>
                <img src="' . ($photos > 0 ? '/upload/objects/s' . $id . '_'.$m.'.jpg' : '/upload/snophoto.gif') . '" class="rounded" width="100" alt="' . $street . '" /></a><br />';
		} else {
			$returnHtml .= '<a href="' . $object_url . '" ><img src="/upload/snophoto.gif" class="rounded" width="100" border="0" alt="' . $street . '" /></a><br />';
		}
		if ($type == 20) {
			if ($orient) {
				$returnHtml .= '<a class="object_url" href="' . $object_url . '" >' . $orient . '</a><br />';
			}
		} else if ($type == 1) {
			$returnHtml .= str_replace('ые квартиры', 'ая', $rooms) . '<br/>';
		}
		$returnHtml .= '<span class="street_one">' . $street . '</span>';
		if ($orient && $type != 20) {
			$returnHtml .= ' / <span sclass="orient_color">' . $orient . '</span>';
		}
		$returnHtml .= '<br/><span class="price_color">Цена: ' . $price . '</span>';
		$returnHtml .= '<div id="preview_m' . $id . '_0" class="preview_block" style="width:450px; height:270px; overflow:hidden; border:5px solid #63C; background-color:#FFF;" ><div style="margin-top:-20px;"><h2>' . $street;
		if ($type != 20) {
			$returnHtml .= "[" . $filial_pref . "-" . $id1c . "]";
		}
		$returnHtml .= '</h2></div> <div class="imgrounded" style="margin-top:-43px; float:left; width:250px; height:250px; overflow:hidden;"><img alt="' . $street;
		if ($type != 20) {
			$returnHtml .= "[" . $filial_pref . "-" . $id1c . "]";
		}
		$returnHtml .= '"src="/upload/objects/m' . $id . '_0.jpg" /> </div>';
		if ($type == '20') {
			$returnHtml .= '<div style="font-size:11px; text-align:justify; color:#666; padding:6px;  margin-top:-43px; height:70px; float:left; width:180px; border:#000 0px solid;  "><h2>' . $orient . '</h2></div>';
		} else {
			$returnHtml .= '<div style="font-size:11px; text-align:justify; color:#666; padding:6px;  margin-top:-43px; height:135px; float:left; width:180px; border:#000 0px solid;  ">' . $descr = str_replace("@@@", "", $descr) . '</div>';
		}
		$returnHtml .= '<div style="font-size:11px; text-align:justify; color:#666; padding:6px; margin-top:0px; height:135px; float:left; width:180px; border:#000 0px solid;"><span style="color:#333;">';
		if ($rooms)
			$returnHtml .= $rooms . ",";
		if ($dtype)
			$returnHtml .= $dtype . ",";
		if ($status)
			$returnHtml .= $status . ",";
		$returnHtml .= '</span>  <span style="color:#333;">Цена: </span><span style="color:#F00;font-size:12px; font-weight:bold;">' . $price . '</span>';
		if ($filial_info) {
			$returnHtml .= '<br /><span style="color:#000; font-size:14px; ">Телефон филиала:<br /> </span><span style="color:#F00;font-size:16px;  font-weight:bold;" >' . $filial_info . '</span>';
		}
		$returnHtml .= '<br /> <span style="color:#63C;"> ';
		if ($type == 20) {
			$returnHtml .= '  Планировки квартир смотрите на странице объекта ';
		} else {
			$returnHtml .= ' Больше фото на странице объекта ';
		}
		$returnHtml .= ' </span></div></div></td>';
		return $returnHtml;
	}
}
?>
