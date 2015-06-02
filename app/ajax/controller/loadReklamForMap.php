<?php

class Ajax_Controller_LoadReklamForMap  extends K_Controller_Ajax {

    public function mapReklamAction(){

        $html = '';

        $items = K_Q::row('SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,jk.type_typejk_name type,a.price price,cu.name cur,imf.img first_img FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN objects_img imf ON imf.id_add=a.id_add AND imf.first=1
                      WHERE a.id='.$_GET['idobj'].' GROUP BY a.id');


        $html = $html . '<div class="rec-head">Рекомендуем в стране '.$items['country'].'</div>
                    <div class="rec-img">
                        <img src="/upload/'.$items['first_img'].'" width="500px" height="120px" />

                        <div class="rec-info">
                            '.$items['type'].' <br><span>'.$items['price'].'</span>
                        </div>
                    </div>';


        $this->putAjax($html);
    }
}