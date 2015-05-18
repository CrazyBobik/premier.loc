<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_Role extends Model {
    var $name = 'role';
    var $primary = 'role_id';
        /*var $validate = array(
            'user_name' => array('required' => true),
            'user_login' => array('required' => true, 'userExists'),
            'user_password' => array('required' => true, 'pwdTest'),
            'user_email' => array(
                'required' => true,
                'lengthTest',
            'email',
            'userExists'));*/

    protected function roleExists(&$text, $fieldName) {

        $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text, )));
        if (count($result)) {
            $this->errors[$fieldName] = 'Роль с таким названием или ACL key уже есть в системе';
            return false;
        }

        return true;
    }

    protected function roleExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 'not' => array('role_id' => $this->data['role_id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Роль с таким названием или ACL key уже есть в системе';
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