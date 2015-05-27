<?php

class Site_Model_ObjectImport extends Site_Model_Object {

    public function addImport($item, $importId){

        $data = array();

        $id = k_q::one('SELECT id FROM objects WHERE from_import="'.$importId.'" AND imp_id ='.$item['id']);

        if($id){
            $outArray = array(
                'error' => true,
                'msg' => "Объявление с id импорта №".$item['id']." уже было импортировано",
                'id' => $id
            );

            return $outArray;
        }

        $data['imp_id'] = $item['id'];

//        if (isset($this->arrayCountry[$item['country']])){
//            $data['country'] = $this->arrayCountry[$item['country']];
//        } else {
//            return $this->getErr('country');
//        }
//
//        if (isset($this->arrayRegion[$item['region']])){
//            $data['region'] = $this->arrayRegion[$item['region']];
//        } else {
//            return $this->getErr('region');
//        }
//
//        if (isset($this->arrayCity[$item['city']])){
//            $data['city'] = $this->arrayRegion[$item['region']];
//        } else {
//            return $this->getErr('region');
//        }




        $this->init();
    }

    public function updateImport(){
        $this->init();
    }

//    private function getErr($mess){
//        $outArray = array(
//            'error' => true,
//            'msg' => "Не коректное поле ".$mess
//        );
//
//        return $outArray;
//    }
}