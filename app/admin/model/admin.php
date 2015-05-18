<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_Admin extends Model {
    var $name = 'admins';
    var $primary = 'admin_id';

    protected function pwdTest(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') < 5) {
            $this->errors[$fieldName] = 'Минимальная длина пароля 5 символов';
            return false;
        }
        if (mb_strlen($text, 'UTF-8') > 20) {
            $this->errors[$fieldName] = 'Максимальная длина пароля 20 символов';
            return false;
        }
        return true;
    }
    
       protected function pwdTestUpdate(&$text, $fieldName) {
        if(mb_strlen($this->data['admin_password'])>0){
            if (mb_strlen($text, 'UTF-8') < 5) {
                $this->errors[$fieldName] = 'Минимальная длина пароля 5 символов';
                return false;
            }
            if (mb_strlen($text, 'UTF-8') > 20) {
                $this->errors[$fieldName] = 'Максимальная длина пароля 20 символов';
                return false;
            }
        }
        return true;
    }

    protected function adminExists(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
        if (count($result)) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом или логином уже зарегестрирован';
            return false;
        }
       return true;
    }

    protected function adminExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 'not' => array('admin_id' => $this->data['admin_id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом или логином уже зарегестрирован';
            return false;
        }
        return true;
    }

    protected function lengthTest(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') > 255) {
            $this->errors[$fieldName] = 'Максимальная длина поля 255 символов';
            return false;
        }
        if (mb_strlen($text, 'UTF-8') < 1) {
            $this->errors[$fieldName] = 'Заполните это поле, пожалуйста';
            return false;
        }
        return true;
    }


}

?>