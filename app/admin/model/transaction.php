<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_Transaction extends Model {
    var $name = 'transactions';
    var $primary = 'transaction_id';
    var $validate  = array(
                       'transaction_organization' => array('notEmpty','int','organizationExists'),
                       'transaction_client' => array('notEmpty','int','clientExists'),
                       'transaction_pay_amount' => array('notEmpty','numeric','amountCheck'),
                       
                       'transaction_pay_method' => array('notEmpty','enum'=>array('invoise','card')),                            
                       'transaction_comment' => array('notEmpty','minlen'=>'5','maxlen'=>'255')
                       );
                       
                       
                             
    protected function amountCheck(&$text, $fieldName) {
       $summ=floatval($text);
         if ($summ > 0.01 && $summ < 99999){
            return true;
         }else{
            $this->errors[$fieldName] = 'Суммы перевода должна быть в диапозоне от 0,01 до 99999'; 
            return false;
         }
    }                
    protected function organizationExists(&$text, $fieldName) {
      $ortganizationModel= new Admin_Model_Organization;
      $result = $ortganizationModel->fetchRow(K_Db_Select::create()->where(array('organization_id' => $text)));
        if (count($result)) {
           return true;
        } 
       $this->errors[$fieldName] = 'Такой организации не существует';
       return false;
    }
    
    protected function clientExists(&$text, $fieldName) {
      $clientModel= new Admin_Model_Client;
      $result = $clientModel->fetchRow(K_Db_Select::create()->where(array('client_id' => $text)));
        if (count($result)) {
           return true;
        } 
       $this->errors[$fieldName] = 'Такого клиента не существует';
       return false;
    }
    
    
}

?>