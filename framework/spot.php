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
