<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');
class Admin_Model_Client extends Model {
    var $name = 'clients';
    var $primary = 'client_id';

  
    protected function pwdTest(&$text, $fieldName) {
        if (mb_strlen($text, 'UTF-8') < 5) {
            $this->errors[$fieldName] = 'Минимальная длина пароля 5 символов';
            return false;
        }
        
        if (mb_strlen($text, 'UTF-8') > 20) {
            $this->errors[$fieldName] = 'Максимальная длина пароля 20 символов';
            return false;
        }
        
        if($text!=$this->data['client_password2']){
           $this->errors[$fieldName] = 'Пароли не совпадают'; 
           return false;  
        }
        return true;
    }
    
    protected function pwdTestUpdate(&$text, $fieldName) {
        if(mb_strlen($this->data['client_password'])>0){
            if (mb_strlen($text, 'UTF-8') < 5) {
                $this->errors[$fieldName] = 'Минимальная длина пароля 5 символов';
                return false;
            }
            if (mb_strlen($text, 'UTF-8') > 20) {
                $this->errors[$fieldName] = 'Максимальная длина пароля 20 символов';
                return false;
            }
            
           if($text!=$this->data['client_password2']){
               $this->errors[$fieldName] = 'Пароли не совпадают'; 
               return false;  
           }
        }
        return true;
    }
    
    protected function pwdTestOneUpdate(&$text, $fieldName) {
        if(mb_strlen($this->data['client_password'])>0){
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

    protected function clientExists(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
        if ($result && count($result)) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом  уже зарегестрирован';
            return false;
        }
       return true;
    }

    protected function clientNotExists(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
        if ($result && count($result)) {
          return true;
        } 
       $this->errors[$fieldName] = 'Пользователя с таким email`ом не существует';
       return false;
    }


    protected function clientExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 'not' => array('client_id' =>             $this->data['client_id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом уже существует';
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

   protected function organizationIdNotExists(&$text, $fieldName){
    $organizationModel = new Admin_Model_Organization;
       $result = $organizationModel->fetchRow(K_Db_Select::create()->where(array('organization_id' => $text)));
        if ($result && count($result)) {
           return true;
        }
        $this->errors[$fieldName] = 'Такой организации не сеществует';
           return false;
       
    }
   protected function clientPhone(&$text, $fieldName){
        if(!$text){
           return true;
        }
        
        if(!preg_match('/^[0-9+() -]+$/',$text)){
           $this->errors[$fieldName] = 'В номере телефона допускаються только цифры и символы: +, (, ),-';
           return false;
        }
    
       if(strlen($text)<6){
           $this->errors[$fieldName] = 'Номер телефона не может быть короче 6 символов';
           return false;
        }else{
           return true;
        }
        
     }  
 
}

?>