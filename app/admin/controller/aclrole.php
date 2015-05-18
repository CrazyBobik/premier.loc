<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_AclRole extends Controller
{
   private $dictionary=array(
       'role_name'=>'Имя роли',
       'role_acl_key'=>'ACL key',   
       'admin_password'=>'Пароль', 
       'rule_role_id'=>'Роль',   
       'rule_resource_id'=>'Ресурс',   
       'rule_type'=>'Тип',   
       'rule_privilege_id'=>'Привелегия',   
   );
   
   
   	public function onInit() {
                $this->formDictionary = new K_Dictionary();
                $this->formDictionary->loadFromIni( ROOT_PATH.'/configs/forms/errors.txt');
                K_Validator::setDefaultDictionary( $this->formDictionary );
         }       
   
    public function saveAction()
    {
        $returnJson = array();
        $returnJson['error'] = false;
        $roleModel = new Admin_Model_Role();
        $data = array(
            'role_name' => $_POST['role_name'],
            'role_acl_key' => $_POST['role_acl_key']);
          
        $validate = array(
            'role_name' => array(
               'required' => true,
               'alphanumeric',
               'roleExists'),
            'role_acl_key' => array(
                'required' => true,
                'ealphanumeric',
                'roleExists'));

        // var_dump($validate);

        if ($_POST['save_type'] == 'update')
        {
            $data['role_id'] =intval($_POST['role_id']);
            $validate['role_name'][1] = 'roleExistsUpdate';
            $validate['role_acl_key'][1] = 'roleExistsUpdate';
        }
        
        if ($roleModel->isValidRow($data, $validate))
        {
            // узнаём уровень вложенности предка
            $parentId=intval($_POST['role_parent_id']);
            
            if ($parentId)
            {
               $result = $roleModel->find(K_Db_Select::create()->fields('role_level,role_name')->where(array('role_id' =>$parentId)));
                if (isset($result) && is_array($result) && count($result))
                {
                    $roleParent = $result[0]->toArray();
                    $roleParentName = $roleParent['role_name'];
                    $data['role_parent_id'] = $parentId;
                    $data['role_level'] = $roleParent['role_level'] + 1;
                } else
                {
                    $data['role_level'] = 0;
                    $data['role_parent_id'] = null;
                    $roleParentName = 'Нет предка';
                }
            }
            else
                {
                    $data['role_level'] = 0;
                    $data['role_parent_id'] = null;
                    $roleParentName = 'Нет предка';
                }
            
            if ($_POST['save_type'] == 'add')
            {
                $role_id = $roleModel->save($data);
                $returnJson['role']['type'] = 'add';
                $returnJson['role']['id'] = $role_id;

            } else
                if ($_POST['save_type'] == 'update')
                {
                    $role_id = $roleModel->update($data, array('role_id' => (int)$_POST['role_id']));
                    $returnJson['role']['type'] = 'update';
                    $role_id = intval($_POST['role_id']);
                    $returnJson['role']['id'] = $_POST['role_id'];
                }

            $returnJson['role']['name'] = $data['role_name'];
            $returnJson['role']['role_acl_key'] = $data['role_acl_key'];
            $returnJson['role']['parentid'] = $data['role_parent_id']==null? '': $data['role_parent_id'] ;
            $returnJson['role']['parentname'] = $roleParentName;
            $returnJson['error'] = false;
            $returnJson['msg'] ="<strong>OK:</strong>Роль удачно сохранена теперь вы можете настроить доступы для неё";

        } else
        {
            $returnJson['error'] = true;
            $returnJson['msg'] = $roleModel->getErrorsD($this->dictionary);
        }
        $this->putJSON($returnJson);
    }


    public function delAction()
    {
        $roleModel = new Admin_Model_role;
        $role_id = $_POST['delroleid'];
        $roleModel->removeID($role_id);
        $userRolesModel = new Admin_Model_AdminRole;
        $userRolesModel->remove(K_Db_Select::create()->where(array('asrol_role_id' => $role_id)));
        $ruleModel = new Admin_Model_Rule();
        $ruleModel->remove(K_Db_Select::create()->where(array('rule_role_id' => $role_id)));
        
        $returnJson = array('error' => false, 'msg' =>
                            '<strong>OK:</strong>Роль удалёна');
        $this->putJSON($returnJson);
    }


    public function saveRuleAction()
    {
        $returnJson = array();
        $returnJson['error'] = false;
        
          if (isset($_POST['rule_resource_id'])&& $_POST['rule_resource_id']!=null && !is_numeric($_POST['rule_resource_id']) ){
             $this->saveTreeRule(); //редиректим на метод обработки правил дерева
          }
        
        $data = array(
            'rule_type' => $_POST['rule_type'],
            'rule_privilege_id' => $_POST['rule_privilege_id'],
            'rule_role_id' => $_POST['rule_role_id']);
        $data['rule_resource_id'] = $_POST['rule_resource_id'];
        
        $ruleModel = new Admin_Model_Rule();

        $validate = array(
            'rule_role_id' => array('required' => true,"int"),
            'rule_resource_id' => array('required' => true,"int"),
            'rule_type' => array('required' => true,"int"),
            'rule_privilege_id' => array(
                'required' => true,
                'int',
                'ruleExists'));

        if ($_POST['save_type'] == 'update')
        {
             $data['rule_id']=intval($_POST['rule_id']);  
             $validate['rule_privilege_id'][1]='ruleExistsUpdate';
        }
      
        if ($ruleModel->isValidRow($data, $validate))
        {
            if ($_POST['save_type'] == 'add')
            {
                $rule_id = $ruleModel->save($data);
                $returnJson['save_type'] = 'add';
            } else
            if ($_POST['save_type'] == 'update')
                {
                    unset($data['rule_id']);  
                    $rule_id = intval($_POST['rule_id']); 
                    $ruleModel->update($data, array('rule_id' =>$rule_id));
                    $returnJson['save_type'] = 'update';
                }
                
            $returnJson['rule']['resource_id'] = $_POST['rule_resource_id'];
            $returnJson['rule']['type'] = $_POST['rule_type'];
            $returnJson['rule']['privilege_id'] = $_POST['rule_privilege_id'];
            $returnJson['rule']['id'] = $rule_id;
            $returnJson['error'] = false;
            $returnJson['msg']="<strong>OK:</strong>Доступ удачно сохранён";
    } else
        {
            $returnJson['error'] = true;
            $returnJson['msg'] =$ruleModel->getErrorsD($this->dictionary);
        }
        $this->putJSON($returnJson);
    }
    
    
     private function saveTreeRule()
    {
        $returnJson = array();
        $returnJson['error'] = false;
        
        $data = array(
            'tree_rule_type' => $_POST['rule_type'],
            'tree_rule_privilege_id' => $_POST['rule_privilege_id'],
            'tree_rule_role_id' => $_POST['rule_role_id']);
                    
        if (preg_match('/^t_([0-9]+)$/', $_POST['rule_resource_id'], $m)){
             $data['tree_rule_resource_id'] = $m[1];
            }
   
       $ruleModel = new Admin_Model_TreeRule(); 
       
       $validate = array(
            'tree_rule_role_id' => array('required' => true,"int"),
            'tree_rule_resource_id' => array('required' => true,"int"),
            'tree_rule_type' => array('required' => true,"int"),
            'tree_rule_privilege_id' => array(
                'required' => true,
                'int',
                'ruleExists'));

        if ($_POST['save_type'] == 'update')
        {
           if (preg_match('/^t_([0-9]+)$/', $_POST['rule_id'], $m)){
              $rule_id=$data['tree_rule_id'] = $m[1];
             $ruleModel = new Admin_Model_TreeRule(); 
            }

           //  $data['tree_rule_id']=intval($_POST['rule_id']);  
             $validate['tree_rule_privilege_id'][1]='ruleExistsUpdate';
        }
      
        if ($ruleModel->isValidRow($data, $validate))
        {
            if ($_POST['save_type'] == 'add')
            {
                $rule_id = $ruleModel->save($data);
                $returnJson['save_type'] = 'add';
            } else
            if ($_POST['save_type'] == 'update')
                {
                    unset($data['tree_rule_id']);   
                    $ruleModel->update($data, array('tree_rule_id' =>$rule_id));
                    $returnJson['save_type'] = 'update';
                    $returnJson['rule']['id'] = $_POST['rule_id'];
                }
            $returnJson['rule']['resource_id'] = $_POST['rule_resource_id'];
            $returnJson['rule']['type'] = $_POST['rule_type'];
            $returnJson['rule']['privilege_id'] = $_POST['rule_privilege_id'];
            $returnJson['rule']['id'] ='t_'.$rule_id;
            $returnJson['error'] = false;
            $returnJson['msg']="<strong>OK:</strong>Доступ удачно сохранён";
        } else
        {
            $returnJson['error'] = true;
            $returnJson['msg'] =$ruleModel->getErrorsD($this->dictionary);
        }
        $this->putJSON($returnJson);
    }
 
    public function delRuleAction()
    {
        if (!is_numeric($_POST['delruleid']) && preg_match('/^t_([0-9]+)$/', $_POST['delruleid'], $m)){
             $rule_id = $m[1];
             $ruleModel = new Admin_Model_TreeRule(); 
            }
          else {
             $rule_id = intval($_POST['delruleid']);
             $ruleModel = new Admin_Model_Rule();
        }
        
        $ruleModel->removeID($rule_id);
        $returnJson = array('error' =>false,'msg' =>'<strong>OK:</strong>Доступ удалён');
        $this->putJSON($returnJson);
    }

    public function getRulesAction()
    {
        $role_id = $this->getParam('roleid');

        $sql = "SELECT DISTINCT rule.rule_resource_id as resource_id, rule.rule_privilege_id as privilege_id,  rule.rule_id as rule_id, rule.rule_type as rule_type, role.role_acl_key as role, resource.resource_name as resource, privilege.privilege_name as privilege 
            FROM rule, role, resource, privilege 
            WHERE rule.rule_role_id='" . intval($role_id) . "'
                  and rule.rule_role_id = role.role_id 
                  and rule.rule_resource_id = resource.resource_id 
                  and rule.rule_privilege_id = privilege.privilege_id 
                  ORDER BY rule.rule_order";
		
	//	var_dump($sql);
		
        $query = new K_Db_Query;
        $Rules = $query->q($sql);
        $roleRules=array();
        foreach ($Rules as $v)
        {
            $rula['type'] = $v['rule_type'] == "allow" ? 'разрешить' : 'запретить';

            $rula['typeid'] = $v['rule_type'] == "allow" ? '1' : '2';

            $rula['resource'] = $v['resource'] == null ? 'Весь сайт' : $v['resource'];

            $rula['privilege'] = $v['privilege'] == null ? 'Полный доступ' : $v['privilege'];

            $rula['rid'] = $v['resource_id'];

            $rula['pid'] = $v['privilege_id'];

            $roleRules[$v['rule_id']] = $rula;
        }
        $sql = "SELECT DISTINCT tree_rule.tree_rule_resource_id as resource_id, tree_rule.tree_rule_privilege_id as privilege_id,  tree_rule.tree_rule_id as rule_id, tree_rule.tree_rule_type as rule_type, role.role_acl_key as role, tree.tree_title as resource, privilege.privilege_name as privilege 
            FROM tree_rule, role, tree, privilege
            WHERE tree_rule.tree_rule_role_id='" . intval($role_id) . "'
                  and tree_rule.tree_rule_role_id = role.role_id 
                  and tree_rule.tree_rule_resource_id = tree.tree_id 
                  and tree_rule.tree_rule_privilege_id = privilege.privilege_id 
                  ORDER BY tree_rule.tree_rule_order";
        $query = new K_Db_Query;
        $Rules = $query->q($sql);
        
        foreach ($Rules as $v)
        {
            $rula['type'] = $v['rule_type'] == "allow" ? 'разрешить' : 'запретить';

            $rula['typeid'] = $v['rule_type'] == "allow" ? '1' : '2';

            $rula['resource'] = $v['resource'] == null ? 'Весь сайт' : $v['resource'];

            $rula['privilege'] = $v['privilege'] == null ? 'Полный доступ' : $v['privilege'];

            $rula['rid'] = 't_'.$v['resource_id'];

            $rula['pid'] = $v['privilege_id'];

            $roleRules['t_'.$v['rule_id']] = $rula;
        }
        
        $returnJson = array(
            'error' => false,
            'rules' => $roleRules
            );
        $this->putJSON($returnJson);
    }
    
    
    public function loadAction() {
        $userModel = new Admin_Model_User;
        $page=intval($_POST['page']);
        $onPage=intval($_POST['onPage']);
        $filter=$_POST['filter'];
    
        if($page){  
            if (!$onPage){
               $onPage=10;  
            }
            
            $start = $page * $onPage - $onPage;
       
        }else
        {
            $start = 0;
            $page = 1;
            $onPage=10;
        }
        
        $where='WHERE 1=1';
        
        if($filter){
          $where ="WHERE r.role_name like ".K_Db_Quote::quote($filter.'%');
        }
        
        $query = new K_Db_Query;
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.*, p.role_name AS parent, (SELECT count(*) as rule_count FROM rule WHERE rule_role_id=r.role_id) as rule_count FROM role AS r LEFT JOIN role AS p ON r.role_status = 1 AND p.role_status = 1 AND r.role_parent_id = p.role_id  $where  ORDER BY r.role_level LIMIT $start, $onPage";
        
        //$sql = "SELECT SQL_CALC_FOUND_ROWS u.*, (SELECT GROUP_CONCAT(role_name SEPARATOR ', ') as user_roles FROM users_roles ur left join role  on ur.usrol_role_id=role_id WHERE ur.usrol_user_id = u.user_id) as user_roles FROM users u  
              //  $where order by user_name LIMIT $start, $onPage";
  
        $rolseRes = $query->q($sql);  
              
        $sql ="SELECT FOUND_ROWS() as countItems;";
        $countItems = $query->q($sql);   
        $countItems=$countItems[0]['countItems'];
        
           $roles=array();         
             foreach($rolseRes as $v){
   
              $id=$v['role_id'];
         
              $roleRow['name']=$v['role_name'];
              $roleRow['role_acl_key']=$v['role_acl_key'];
              $roleRow['parentid']= $v['role_parent_id']==null? '' : $v['role_parent_id'] ;
              
              $roleRow['parentname']= $v['parent']==null ? 'Нет предка' : $v['parent'];
              $roleRow['rule_count']=$v['rule_count'];
              
              $roles[$id]=$roleRow;       
             }        
          
        $returnJson = array('error' => false,
                            'items'=>$roles,
                            'countItems'=>$countItems
                           );
  
        $this->putJSON($returnJson);
    }

	
}