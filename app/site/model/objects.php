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

        $this->cur = array(1, 2, 3, 4);
        $this->garden = array(0, 1);

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


//public function readXML($xml){
//    $reader = new XMLReader();
//    $reader->open($xml);
//
//    while ($reader->read()) {
//
//        if ($reader->localName == 'realty') {
//            $item = array();
//            while ($reader->read()){
//                if ($reader->nodeType == XMLReader::ELEMENT) {
//                    $name = strtolower($reader->localName);
//                    $reader->read();
//                    if (isset($item[$name]) && is_array($item[$name])){
//                        $item[$name]['value'] = $reader->value;
//                    }else
//                        $item[$name] = $reader->value;
//
//                }
//                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'item')
//                    break;
//            }
//            // в этом месте мы уже имеем сформированный массив и можем передать его на какую либо обработку
//            print_r($item);
//        }
//
//    }
//}


}