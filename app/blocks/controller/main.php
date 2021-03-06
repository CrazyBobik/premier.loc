<?php

class Blocks_Controller_Main  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        //Для первого слайдера
        $this->view->menu1 = array();
        $temp = K_TreeQuery::crt("/slider1/")->type(array('slide'))->go();
        $id = ' WHERE a.id IN (';
        foreach($temp as $m){
            $hasCountry = false;
            if (!empty($m["idelem1"])){
                $id = $id.$m["idelem1"].',';
                $hasCountry = true;
            }
            if (!empty($m["idelem2"])){
                $id = $id.$m["idelem2"].',';
                $hasCountry = true;
            }
            if (!empty($m["idelem3"])){
                $id = $id.$m["idelem3"].',';
                $hasCountry = true;
            }
            if ($hasCountry) {
                $this->view->menu1[] = $m;
            }
        }
        $id = substr($id, 0, strlen($id)-1);
        $id = $id.')';

        $this->view->slider = K_Q::data('SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name region,ci.type_city_name city,m.name market,jk.type_typejk_name type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,cu.name cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,s.name state,imf.img first_img FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      LEFT JOIN objects_img imf ON imf.id_add=a.id_add AND imf.first=1
                      '.$id.' GROUP BY a.id');

        //для второго слайдера
        $this->view->menu2 = array();
        $temp = K_TreeQuery::crt("/slider2/")->type(array('slide'))->go();
        $id = ' WHERE a.id IN (';
        foreach($temp as $m){
            $hasCountry = false;
            if (!empty($m["idelem1"])){
                $id = $id.$m["idelem1"].',';
                $hasCountry = true;
            }
            if (!empty($m["idelem2"])){
                $id = $id.$m["idelem2"].',';
                $hasCountry = true;
            }
            if (!empty($m["idelem3"])){
                $id = $id.$m["idelem3"].',';
                $hasCountry = true;
            }
            if ($hasCountry) {
                $this->view->menu2[] = $m;
            }
        }
        $id = substr($id, 0, strlen($id)-1);
        $id = $id.')';

        $this->view->slider2 = K_Q::data('SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name region,ci.type_city_name city,m.name market,jk.type_typejk_name type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,cu.name cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,s.name state,imf.img first_img FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      LEFT JOIN objects_img imf ON imf.id_add=a.id_add AND imf.first=1
                      '.$id.' GROUP BY a.id');


        $this->render('main');
 	}
}