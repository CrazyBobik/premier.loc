<?php

class Site_Model_UserPhone extends Model {
    
    var $name = 'users_phones';
    var $primary = 'id';
    
    protected function userExists(&$text, $fieldName) {
        
       if(iaset($this->data[$this->primary]) && !empty($this->data[$this->primary])){
        
           $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 
                                                                        'not' => array($this->primary => $this->data[$this->primary])))));
                                                                        
       }else{
       
         $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
       }
      
        if (count($result)) {
            $this->errors[$fieldName] = 'Такой телефон уже ';
            return false;
        }
       return true;
       
       
    }
    

}

?>