<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_Rule extends Model {
    var $name = 'rule';
    var $primary = 'rule_id';

 
  protected function ruleExists(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array(
                                  'rule_role_id'=>$this->data['rule_role_id'],
                                  'rule_resource_id'=>$this->data['rule_resource_id'],
                                   $fieldName => $text))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Доступ с таким сочетание ресурса и привелегии уже есть в системе';
            return false;
        }
       return true;
    }
  
    protected function ruleExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array(
                                   'rule_role_id'=>$this->data['rule_role_id'],
                                   'rule_resource_id'=>$this->data['rule_resource_id'],
                                    $fieldName => $text, 
                                   'not' => array('rule_id' => $this->data['rule_id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Доступ с таким сочетание ресурса и привелегии уже есть в системе';
            return false;
        }
        return true;
    }

}

?>