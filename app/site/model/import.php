<?php

class Site_Model_Import extends Model {
    var $name = 'import';
    var $primary = 'id';

    var $isInit = null;

    public function init(){
        if ($this->isInit){
            return;
        }

        $rows = K_Q::data('SELECT * FROM import');

        $this->array = mysql_fetch_array($rows);

        $this->isInit = true;
    }

    public function add($data, $import = false){

        $this->init();

        $validate = array(
            'import_url' => array( 'required' => true,),
            'format' => array( 'required' => true, ),
            'limit' => array( 'required' => true, ),
            'infeed' => array( 'required' => true, ),
            'processed' => array( 'required' => true, ),
            'publicated' => array( 'required' => true,),
            'updated' => array( 'required' => true, ),
            'deleted' => array( 'required' => true, ),
            'work_now' => array( 'required' => true, ),
            'erorrs' => array( 'required' => true, ),
            'proxy' => array( 'required' => true),
            'add_date' => array( 'required' => true),
            'start_date' => array( 'required' => true, )
        );

        //var_dump($validate['category']);
        $this->fieldsNames = array(

            'country' => t('Страна:','Country:'),
            'import_url' => t('URL импорта', 'Import URL'),
            'format' => t('Формат', 'Format'),
            'limit' => t('Лимит', 'Limit'),
            'infeed' => t('В файле', 'In file'),
            'processed' => t('Обработано', 'Processed'),
            'publicated' => t('Опубликовано', 'Publicated'),
            'updated' => t('Обновлено', 'Updated'),
            'deleted' => t('Удалено', 'Deleted'),
            'work_now' => t('Работает сечас', 'Work now'),
            'erorrs' => t('Ошибки', 'Erorrs'),
            'proxy' => t('Прокси', 'Proxy'),
            'add_date' => t('Дата добавления', 'Add date'),
            'start_date' => t('Дата запуска', 'Start date')

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