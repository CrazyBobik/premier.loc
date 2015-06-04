<?php

class Site_Model_Objects extends Model {
    var $name = 'objects';
    var $primary = 'id';

    var $arrayCountry = array();
    var $arrayRegion = array();
    var $arrayCity = array();

    var $isInit = null;

    public function init(){
        if ($this->isInit){
            return;
        }

        $rows = K_TreeQuery::crt("/allcountry/")->type(array('country', 'region', 'city'))->go();

        foreach ($rows as $v){
            if ($v['tree_type'] === 'country'){

               $this->arrayCountry[] = $v['tree_id'];

            } elseif ($v['tree_type'] === 'region'){

                $this->arrayRegion[$v['tree_pid']][] = $v['tree_id'];

            } elseif ($v['tree_type'] === 'city'){

                $this->arrayCity[$v['tree_pid']][] = $v['tree_id'];
            }
        }



        $this->market = array(1, 2);

        $rows = K_TreeQuery::crt("/type/")->type(array('typejk'))->go();
        foreach  ($rows as $v){
            $this->type[] = $v['tree_id'];
        }

        $this->isInit = true;
    }

    public function add($data, $import = false){

        $this->init();

        $validate = array(
            'country' => array( 'required' => true, 'enum'=>$this->arrayCountry),
            'region' => array( 'required' => true, 'enum'=>$this->arrayRegion[$data['country']]),
            'city' => array( 'required' => true, 'enum'=>$this->arrayCity[$data['region']]),
            'market' => array( 'required' => true, 'enum'=> $this->market),
            'type' => array( 'required' => true, 'enum'=> $this->types),
            'all_sq' => array( 'required' => true,'min'=>10, 'max'=>5000),
            'living_sq' => array( 'required' => true, 'min'=>5, 'max'=>5000),
            'kithcen_sq' => array( 'required' => true, 'min'=>5, 'max'=>1000),
            'price' => array( 'required' => true, 'min'=> 5000),
            'cur' => array( 'required' => true, 'enum'=>$this->cur ),
            'to_sea' => array( 'required' => true),
            'to_airport' => array( 'required' => true),
            'rooms' => array( 'required' => true, 'min'=>1, 'max'=>100),
            'floor' => array( 'required' => true, 'min'=>1),
            'all_floors' => array( 'required' => true, 'min'=>1),
            'garden' => array( 'required' => true, 'enum'=> $this->garden),
            'bath_rooms' => array( 'required' => true, 'min'=>0, 'max'=>100),
            'state' => array( 'required' => true),
            'description' => array( 'required' => true, 'maxlen'=> 5000)
        );

        //var_dump($validate['category']);
        $this->fieldsNames = array(

            'country' => t('Страна:','Country:'),
            'region' => t('Регион:','Region:'),
            'city' => t('Город:','City:'),
            'market' => t('Рынок:','Market:'),
            'type' => t('Тип:','Type:'),
            'all_sq' => t('Полная площадь:','All area:'),
            'living_sq' => t('Жилая площадь:','Living area:'),
            'kithcen_sq' => t('Площадь кухни:','Kithcen area:'),
            'price' => t('Цена:','Price:'),
            'cur' => t('Валюта:','Сurrency:'),
            'to_sea' => t('Растояние до моря:','Distance to the sea:'),
            'to_airport' => t('Растояние до аеропорта:','Distance from the airport:'),
            'rooms' => t('Количество комнат:','Number of rooms:'),
            'floor' => t('Этаж:','Floor:'),
            'all_floors' => t('Количество этажей:','Number of floors:'),
            'garden' => t('Сад:','Garden:'),
            'bath_rooms' => t('Количество ванн:','Number of bathrooms:'),
            'state' => t('Состояние:','State:'),
            'description' => t('Описание:','Вescription:')

        );

        $dictionary = new K_Dictionary;

        $dictionary->loadFromIni(CONFIGS_PATH.'/forms/ads.txt');

        $this->setDictionary($dictionary);


        if ($this->isValidRow($data, $validate)) {
            //var_dump($data);
            //var_dump($this->save($data));
            if(!$this->save($data)) {
                $this->setError( "saveerror", 'SAVE_ERROR', array($test) );
                return false;
            }
            else {

                return true;

            }

        }
        else {
            return false;
        }


    }

    public function deleteGall($id_add){

        $gall = K_q::query("SELECT * FROM objects_img WHERE id_add='".$id_add."'");

        foreach($gall as $v){

            $this->deleteImg($v['img']);

        };

        K_q::query("DELETE FROM objects_img WHERE id_add='".$id_add."'");

    }

// удаляет массив картинок порциями по 50 штук
    public function deleteGallImages($imgs){

        $i = 0;
        $del = array();

        foreach($imgs as $v){

            $del[] = $v;

            if($i>50){

                $this->_deleteGallImages($del);
                $del = array();

            }

            $i++;

        }

        $this->_deleteGallImages($del);

    }

    private function _deleteGallImages($imgs){

        $gall = K_q::query("SELECT img FROM objects_img WHERE id IN (".implode(',', $imgs).")");

//        $delCount = K_q::query('SELECT count(id_add) count, id_add FROM `objects_img` WHERE id IN ('.$_id_array.') group by id_add');

        foreach($gall as $v){

            $this->deleteImg($v['img']);

        }

//        foreach($delCount as $v){
//
//            K_q::query("UPDATE ads set photos_count=photos_count-".$v['count']."  WHERE id_add=".k_q::qv($v['id_add']));
//
//        }

        K_q::query("DELETE FROM objects_img WHERE id IN (".implode(',', $imgs).")");
    }

// удаляет 1 картинку с диска
    public function deleteImg($img){

        if(file_exists(Allconfig::$objImgPaths['original'].$img)){
            unlink(Allconfig::$objImgPaths['original'].$img);
        }
        if(file_exists(Allconfig::$objImgPaths['big'].$img)){
            unlink(Allconfig::$objImgPaths['big'].$img);
        }
        if(file_exists(Allconfig::$objImgPaths['thumb'].$img)){
            unlink(Allconfig::$objImgPaths['thumb'].$img);
        }

    }


    public function deleteObject($id){

        $obj = $this->findId($id);

        if($obj){

            $this->deleteGall($obj['id_add']);
            $this->removeID($id);
//            K_q::query("UPDATE users set colpub_active=colpub_active-1  WHERE id=".k_q::qv($obj['user']));

        }else{

            return false;

        }
    }

    public function deleteObjectGroup($idArrayAll){

        $i = 0;
        $countDelete = 0;

        foreach($idArrayAll as $v){

            $idArray[] = $v;
            $i++;

            if($i>100){

                $countDelete += $this->_deleteObjectGroup($idArray);

            }

        }

        $countDelete += $this->_deleteObjectGroup($idArray);

        return $countDelete;

    }

    public function _deleteObjectGroup($idArray){

        $_id_array = implode(',', $idArray);

        // отнимаем количество активных публикаций

//        $delCount = K_q::query('SELECT count(user) count, user FROM `ads` WHERE id IN ('.$_id_array.') group by user');

        $gall = K_q::query("SELECT g.id_add id_add,g.img img FROM ads a LEFT JOIN gallery g ON a.id_add=g.id_add WHERE a.id IN (".$_id_array.")");

        $id_obj_array = array();

        foreach($gall as $v){

            $this->deleteImg($v['img']);

            $id_obj_array[] = $v['id_add'];
        }

        /*return $_id_array;*/
        K_q::query("DELETE FROM gallery WHERE id_add IN (".implode(',',$id_obj_array).")");
        K_q::query("DELETE FROM objects WHERE id IN (".$_id_array.")");

        $countdelete = count($idArray);

//        foreach($delCount as $v){
//
//            K_q::query("UPDATE users set colpub_active=colpub_active-".$v['count']."  WHERE id=".k_q::qv($v['user']));
//
//        }


        return $countdelete;
    }

    public function genImages($imgPath, $newName){

        require_once LIB_PATH."/img_tool_kit/AcImage.php";

        try {
            $image = AcImage::createImage($imgPath);
        } catch (Exception $e) {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            return false;
        }

        if($image){

            try {
                $image->saveAsJPG(AllConfig::$objImgPaths['original'].$newName);

                if(file_exists($imgPath)){
                    unlink($imgPath);
                }

                //  накладываем лого
                $image->resize(1360, 768)->drawLogo(AllConfig::$objImgPaths['watermarkImport'],2,10)->save(AllConfig::$objImgPaths['big'].$newName);

                if($image = AcImage::createImage(AllConfig::$objImgPaths['big'].$newName)){

                    $image->simpleResize(180, 135)->save(AllConfig::$objImgPaths['thumb'].$newName);

                }

            }catch(Exception $e){

                echo 'Выброшено исключение: ',  $e->getMessage(), ", удалены картинки\n";

                if(file_exists(AllConfig::$objImgPaths['thumb'].$newName)){

                    unlink(AllConfig::$objImgPaths['thumb'].$newName);

                }

                if(file_exists(AllConfig::$objImgPaths['thumb'].$newName)){

                    unlink(AllConfig::$objImgPaths['big'].$newName);

                }

                return false;
            }

        }else{
            return false;
        }
    }

    static public function uniqId($data) {

        $removeFromStreet = Importformats::streetExtractor($data['street']);

        $adsUniqStr = $data['user'];

        $adsUniqStr .= $data['type_propert'].$data['ads_subsec'].$data['type_transac_dop'].$data['category'];

        $adsUniqStr .= $data['region'].$data['city'].$data['zone'].$data['zone2'].$removeFromStreet;

        $adsUniqStr .= $data['rooms'].$data['area1'];

        return $adsUniqStr;

    }
}

