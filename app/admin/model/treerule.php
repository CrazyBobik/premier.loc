<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_TreeRule extends Model {
    var $name = 'tree_rule';
    var $primary = 'tree_rule_id';

 
  protected function ruleExists(&$text, $fieldName) {
      $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array(
                                  'tree_rule_role_id'=>$this->data['tree_rule_role_id'],
                                  'tree_rule_resource_id'=>$this->data['tree_rule_resource_id'],
                                  'tree_rule_privilege_id' => $text))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Доступ с таким сочетанием ресурса и привелегии уже есть в системе';
            return false;
        }
       return true;
    }
  
    protected function ruleExistsUpdate(&$text, $fieldName) {
        $result = $this->fetchRow(K_Db_Select::create()->where(array('and' => array(
                                   'tree_rule_role_id'=>$this->data['tree_rule_role_id'],
                                   'tree_rule_resource_id'=>$this->data['tree_rule_resource_id'],
                                   'tree_rule_privilege_id' => $text, 
                                   'not' => array('tree_rule_id' => $this->data['tree_rule_id'])))));
        if (count($result)) {
            $this->errors[$fieldName] = 'Доступ с таким сочетанием ресурса и привелегии уже есть в системе';
            return false;
        }
        return true;
    }

}

?>