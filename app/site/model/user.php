<?php

class Site_Model_User extends Model {
    
    var $name = 'users';
    var $primary = 'id';

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
        if(mb_strlen($this->data['password'])>0){
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

    protected function userExists(&$text, $fieldName) {
 
       if(isset($this->data[$this->primary]) && !empty($this->data[$this->primary])){
        
           $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 
                                                                                        'not' => array($this->primary => $this->data[$this->primary])))));
                                                                        
       }else{
       
         $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
       }
      
        if (count($result)) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом уже зарегистрирован';
            return false;
        }
       return true;
       
       
    }
    
    protected function userExistsChange(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
      $rw = k_q::query("SELECT * FROM users WHERE id=".$_SESSION['id']."");
        if (count($result)&&($rw['mail'] !== $data['mail'])) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом уже зарегестрирован';
            return false;
        }
       return true;
    }    
    
    protected function checkMailExist(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array($fieldName => $text)));
        if (count($result)>0) {
            return true;
        } 
      $this->errors[$fieldName] = 'Пользователь с таким email`ом не зарегестрирован';
      return false;
    }    

    protected function userExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 'not' => array('id' => $this->data['id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Пользователь с таким email`ом или логином уже зарегестрирован';
            return false;
        }
        return true;
    }

	protected function regionChek(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array($fieldName => $text, 'not' => array('id' => $this->data['id'])))));
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

    protected function singin($login, $password) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array('mail' => $login, 'pass' => md5($password)))));
        if (count($result)) {
           return false;
        }
        return true;
    }
    
    protected function setBalance($balance){
        $this->update(array('balans'=>$balance),array('id'=>K_User::getInfo('id')));
        K_User::setUserKey('balans',$balance);
    }
    
    protected function getBalance($balance){
        $this->update(array('balans'=>$balance),array('id'=>K_User::getInfo('id')));
        K_User::setUserKey('balans',$balance);
    }




}

?>