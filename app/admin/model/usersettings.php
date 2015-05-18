<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_UserSettings extends K_Db_Model {
    var $name = 'users';
    var $primary = 'user_id';

    protected function pwdTest(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') < 5) {
            $this->errors[$fieldName] = 'минимальная длина пароля 5 символов';
            return false;
        }
        if (mb_strlen($text, 'UTF-8') > 20) {
            $this->errors[$fieldName] = 'максимальная длина пароля 20 символов';
            return false;
        }
        if ($this->data['password1'] != $this->data['password2']) {
            $this->errors['password1'] = 'пароли не совпадают';
            return false;
        }
        return true;
    }

    protected function userExists(&$text, $fieldName) {
        
        if ($text != K_Auth::getUserInfo($fieldName)) {
            $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text, )));

            if (count($result)) {
                $this->errors[$fieldName] = 'пользователь с таким email`ом уже зарегестрирован';
                return false;
            }
        }
        return true;
    }
    
    protected function userExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 'not' => array('user_id' => $this->data['user_id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'пользователь с таким email`ом уже зарегестрирован';
            return false;
        }
        return true;
    }
    
    

    protected function userTruePass(&$text, $fieldName) {
        $oldPassword = md5(md5($text . K_Registry::get('Configure.salt')));
        $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $oldPassword)));

        if (!count($result)) {
            $this->errors[$fieldName] = 'неправильный действующий пароль';
            return false;
        }

        return true;
    }
    
       protected function lengthTest(&$text, $fieldName ) {
            if ( mb_strlen( $text, 'UTF-8') > 255 ) {
                    $this->errors[ $fieldName ] = 'максимальная длина поля 255 символов';
                    return false;
            }
            if (  mb_strlen( $text, 'UTF-8') < 1 ) {
                    $this->errors[ $fieldName ] = 'заполните это поле, пожалуйста';
                    return false;
            }
            return true;
    }
}
