<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_AclAdmin extends Controller {

    private $dictionary=array(
       'admin_name'=>'Имя',
       'admin_login'=>'Логин',   
       'admin_password'=>'Пароль',   
       'admin_email'=>'Email',        
    );
    
    public function onInit() {
                $this->formDictionary = new K_Dictionary();
                $this->formDictionary->loadFromIni( ROOT_PATH.'/configs/forms/errors.txt');
                K_Validator::setDefaultDictionary( $this->formDictionary );
         }      


    public function saveAction() {
        $returnJson = array();
        $returnJson['error'] = false;
        $adminModel = new Admin_Model_Admin();
        $data = array(
            'admin_name' =>trim ($_POST['admin_name']),
            'admin_login' =>trim ($_POST['admin_login']),
            'admin_email' =>trim ($_POST['admin_email']),
            'admin_password' =>trim ($_POST['admin_password'])
            );

        $validate = array(
            'admin_name' => array('required' => true),
            'admin_login' => array('required' => true, 'adminExists'),
            'admin_password' => array('pwdTest'),
            'admin_email' => array(
                'required' => true,
                'lengthTest',
                'email',
                'userExists'));

        if ($_POST['save_type'] == 'update') {
                $data['admin_id'] = $_POST['admin_id'];
                $validate['admin_login'][0] = 'adminExistsUpdate';
                $validate['admin_email'][2] = 'adminExistsUpdate';
                $validate['admin_password'][0]= 'pwdTestUpdate';
        }
      
          if ($adminModel->isValidRow($data, $validate)) {
           if ($_POST['save_type'] == 'add') {
                $data['admin_password'] = md5(md5($data['admin_password'] . K_Registry::get('Configure.salt')));
                $admin_id = $adminModel->save($data);
                $returnJson['admin']['type'] = 'add';
                $returnJson['admin']['id'] = $admin_id;
            } else
                if ($_POST['save_type'] == 'update') {
                    if(mb_strlen($data['admin_password'])>0){
                     $data['admin_password'] = md5(md5($data['admin_password'] . K_Registry::get('Configure.salt')));   
                        
                    }else{
                        unset($data['admin_password']);
                    }
                    $admin_id = $adminModel->update($data, array('admin_id' => (int)$_POST['admin_id']));
                    $returnJson['admin']['type'] = 'update';
                    $admin_id = intval($_POST['admin_id']);
                    $returnJson['admin']['id'] = $_POST['admin_id'];
                }

            $adminRolesModel = new Admin_Model_AdminRole;
            $adminRolesModel->remove(K_Db_Select::create()->where(array('asrol_admin_id' => $admin_id)));

            if (isset($_POST['roles']) && count($_POST['roles']) > 0){
                foreach ($_POST['roles'] as $v) {
                    $rd['asrol_admin_id'] = $admin_id;
                    $rd['asrol_role_id'] = intval($v);
                    $rolesData[] = $rd;
                }
                $adminRolesModel->saveAll($rolesData);
                $returnJson['admin']['roles'] = $_POST['roles'];
            }

            $returnJson['admin']['name'] = $data['admin_name'];
            $returnJson['admin']['login'] = $data['admin_login'];
            $returnJson['admin']['email'] = $data['admin_email'];
            $returnJson['error']=false;  
            $returnJson['msg']="<strong>OK:</strong>Пользователь удачно сохранён";
        } else {
            $returnJson['error'] = true;
            $returnJson['msg']=$adminModel->getErrorsD($this->dictionary);
         }
        $this->putJSON($returnJson);
    }

    public function delAction() {
        $adminModel = new Admin_Model_Admin;
        $adminId=intval($_POST['id']);
        $adminModel->removeID($adminId);
        //var_dump($user_id);
        $adminRolesModel = new Admin_Model_AdminRole;

        $adminRolesModel->remove(K_Db_Select::create()->where(array('asrol_admin_id' => $adminId)));

        $returnJson = array('error' => false,
                             'msg'  => '<strong>OK:</strong>Пользователь удалён');
       $this->putJSON($returnJson);
    }
    
    public function delAdminAction() {
        $admin_id=intval($_POST['id']);
        //var_dump($user_id);
        
        k_q::query("delete from admins where admin_id=$admin_id");
        
        $returnJson = array('error' => false,
                             'msg'  => '<strong>OK:</strong>Пользователь удалён');
       $this->putJSON($returnJson);
    }
      
    public function loadAction() {
	
        $adminModel = new Admin_Model_Admin;
        $page = intval($_POST['page']);
        $onPage = intval($_POST['onPage']);
        $filter = $_POST['filter'];
    
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
          $where ="WHERE admin_name like ".K_Db_Quote::quote($filter.'%');
        }
        
        $query = new K_Db_Query; 
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.*, (SELECT GROUP_CONCAT(role_name SEPARATOR ', ') as admin_roles FROM admins_roles ur left join role  on ur.asrol_role_id=role_id WHERE ur.asrol_admin_id = u.admin_id) as admin_roles FROM admins u  
                $where order by admin_name LIMIT $start, $onPage";
				
		//var_dump($sql);
		
        $adminsRes = $query->q($sql);  
              
        $sql ="SELECT FOUND_ROWS() as countItems;";
        $countItems = $query->q($sql);   
        $countItems = $countItems[0]['countItems'];
        
		$admins = array();         
		foreach($adminsRes as $v){

			$id = $v['admin_id'];
			$adminRow['name'] = $v['admin_name'];
			$adminRow['login'] = $v['admin_login'];
			$adminRow['email'] = $v['admin_email'];
			$adminRow['roles'] = $v['admin_roles'] == null ? '' : $v['admin_roles'];
			$admins[$id] = $adminRow;       
		
		}        
          
        $returnJson = array('error' => false,
                            'items'=>$admins,
                            'countItems'=>$countItems
                           );
  
        $this->putJSON($returnJson);
    }

}
