<?php defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_Valid extends Model {
    var $name = 'users';
    var $primary = 'user_id';

      public function phone(&$text, $fieldName){
          
          if(preg_match('/^[\d\(\)\+\- ]{5,25}$/is', $text)){
            
             return true;
                     
          }else{
            
             $this->errors[$fieldName] = t('Неправильный формат телефона, для ввода разрешены только цифры, знаки: +,-,(,) и пробел', 'Неправильний формат телефону, для введення дозволені тільки цифри, знаки: +, -, (,) і пробіл');
             return false;
            
          }
      }

}
?>