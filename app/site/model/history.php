<?php

class Site_Model_History extends Model {
    
    var $name = 'history';
    var $primary = 'id';

    protected function exists(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
        if (count($result)) {
            $this->errors[$fieldName] = '';
            return false;
        }
       return true;
    }

}

?>