<?php

class Dev_Controller_Acl extends Controller{
    
    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru');

    public function init() {
   	    $this->view->title = 'Просмотр пользователей';
		$this->view->headers =array(
                                    array('title'=>'Роли',
                                          'href'=>'/admin/acl/roles',
                                    ),
                                    array('title'=>'Пользователи',
                                          'href'=>'/admin/acl/index',
                                    )
                              );
   }


    protected function indexAction() {
   	    $this->view->title = 'Просмотр пользователей';
	
        $query = new K_Db_Query;
        $sql = "SELECT u.*, (SELECT GROUP_CONCAT(role_name SEPARATOR ', ') as user_roles FROM users_roles ur left join role  on ur.usrol_role_id=role_id WHERE ur.usrol_user_id = u.user_id) as user_roles FROM users u order by user_name";
        $this->view->users = $query->q($sql);
        
        $sql = "SELECT role_id, role_name FROM role";
        $this->view->roles = $query->q($sql);
        $this->render( 'users' );  
        
    }
    
     protected function rolesAction() {
         $this->view->title = 'Просмотр ролей';
	    
         $query = new K_Db_Query; 
         $sql = "SELECT DISTINCT r.*, p.role_name AS parent, (SELECT count(*) as rule_count FROM rule WHERE rule_role_id=r.role_id) as rule_count FROM role AS r LEFT JOIN role AS p ON r.role_status = 1 AND p.role_status = 1 AND r.role_parent_id = p.role_id ORDER BY r.role_level";
         $this->view->roles = $query->q($sql);
       
         $sql = "SELECT DISTINCT resource_name,resource_id FROM resource ORDER BY resource_id";
         $this->view->resurses = $query->q($sql);
         
         $sql = "SELECT DISTINCT privilege_name,privilege_id FROM privilege ORDER BY privilege_id";
         $this->view->privileges = $query->q($sql);
        
         $tree= new K_Tree;
         
         $this->view->treeResurses = $tree->getAllTree();
         $this->render( 'roles' );  
     }
     
     
     protected function reloadAction() {
        K_Access::load(true);  
        K_Access::loadAclTree(true);  
        $returnJson['msg']='<strong>ОК:</strong> ACL перезагружен';
        $returnJson['error']=false;
        $this->putJSON($returnJson);
    }
    
     protected function testAction() {
       
       $this->putJSON(K_Tree::reStoreTreeKeys(0,0));
       
     }
  
}
