<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_ClientForm extends Model {
    var $name = 'type_clientform';
    var $primary = 'type_clientform_id';
 
    protected function length40(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') > 40) {
            $this->errors[$fieldName] = 'максимальная длина этого поля 40 символов';
            return false;
        }
        return true;
    }


    protected function length255(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') > 255) {
            $this->errors[$fieldName] = 'максимальная длина этого поля 255 символов';
            return false;
        }
        return true;
    }

    protected function minlength5(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') < 5) {
            $this->errors[$fieldName] = 'минимальная длина этого поля 5 символов';
            return false;
        }
        return true;
    }


}

?>