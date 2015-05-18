<?php

class Admin_Controller_Acl extends Controller{
    
    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru');

    public function onInit(){
	
   	    $this->view->title = 'Просмотр персонала';
		
		$this->view->menuTabs = array(	'acl'=> array('title'=>'Сотрудники',
													  'href'=>'/admin/acl'
													),
													 
										'roles'=> array('title'=>'Роли',
														'href'=>'/admin/acl/roles'
													),
					
								);
	
								  
   }


    protected function indexAction() {
   	    $this->view->title = 'Просмотр сотрудников';
		$this->view->activeTab = 'acl';
		
        $query = new K_Db_Query;
        $sql = "SELECT a.*, (SELECT GROUP_CONCAT(role_name SEPARATOR ', ') as admin_roles FROM admins_roles ar left join role  on ar.asrol_role_id = role_id WHERE ar.asrol_admin_id = a.admin_id) as admin_roles FROM admins u order by admin_name";
        $this->view->admins = $query->q($sql);
        
        $sql = "SELECT role_id, role_name FROM role";
        $this->view->roles = $query->q($sql);
        $this->render( 'admins' );  
        
    }
    
     protected function rolesAction() {
         $this->view->title = 'Просмотр ролей';
	     $this->view->activeTab = 'roles';
		 
         $query = new K_Db_Query; 
         $sql = "SELECT DISTINCT r.*, p.role_name AS parent, (SELECT count(*) as rule_count FROM rule WHERE rule_role_id=r.role_id) as rule_count FROM role AS r LEFT JOIN role AS p ON r.role_status = 1 AND p.role_status = 1 AND r.role_parent_id = p.role_id ORDER BY r.role_level";
         $this->view->roles = $query->q($sql);
       
         $sql = "SELECT DISTINCT resource_name, resource_id FROM resource ORDER BY resource_id";
         $this->view->resurses = $query->q($sql);
         
         $sql = "SELECT DISTINCT privilege_name, privilege_id FROM privilege ORDER BY privilege_id";
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
    
     protected function restoreTreeKeysAction() {
       
        $this->putJSON(K_Tree::reStoreTreeKeys(0,0));
       
     }
  
}
