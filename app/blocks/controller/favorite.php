<?php

class Blocks_Controller_Favorite  extends K_Controller_Blocks {
	
	public $helpers = array('');
	
	public function onInit() {
	
		 parent::onInit();
 	}
	
	public function indexAction() {

        $arrId = $_COOKIE['favoritearray'];

        $this->view->page = 1;
        $onPage = 10;
        if (isset($_GET['page'])){
            $this->view->page = (int) $_GET['page'];
        }

        $pag_info = K_Paginator::prepear($this->view->page, $onPage);

        $limit = " LIMIT ".$pag_info['start'].", ".$pag_info['onPage'];

        $this->view->fav = K_Q::data('SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name region,ci.type_city_name city,m.name market,jk.type_typejk_name type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,cu.name cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,s.name state,imf.img first_img,GROUP_CONCAT(DISTINCT im.img ORDER BY im.img) imgs FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      LEFT JOIN objects_img imf ON imf.obj_id=a.id AND imf.first=1
                      LEFT JOIN objects_img im ON im.obj_id=a.id WHERE a.id IN ('.$arrId.')
                      GROUP BY a.id'.$limit);

        $countItems = K_Q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
        $this->view->pages = ceil($countItems/$pag_info['onPage']);

		$this->render('favorite'); 
 	}
  
}