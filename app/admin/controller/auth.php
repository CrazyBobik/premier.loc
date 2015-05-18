<?php

defined( 'K_PATH' ) or die( 'DIRECT ACCESS IS NOT ALLOWED' );

class Admin_Controller_Auth extends Controller {

    /* {public} */
    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru' );
    public $layout = 'auth_layout';

    /* {actions} */
    public function indexAction() {
        $auth_error = false;
        //Если авторизован отпровляем в админку
        if ( K_Access::acl()->isAllowed(K_Auth::getRoles(),'admin')) {
             K_Request::redirect( "/admin" );
        }
        if ( isset( $_POST['btnauth'] ) ) {
            $login = K_Arr::get( $_POST, 'login', false );
            if ( $login and strlen( $login ) < 100 ){

                $password = K_Arr::get( $_POST, 'password', false );

                if ( $password ) {
                 $password_hash = md5( md5( $password . K_Registry::get( 'Configure.salt' ) ) );

                    $admin_model = new Admin_Model_Admin;
                    $admin_arr = $admin_model->find( K_Db_Select::create()->fields( 'admin_id, admin_name, admin_login , admin_email' )->where( array( 'and' => array( 'admin_login' => $login, "admin_password" => $password_hash ) ) )->limit(1) );
 
                 	if ( is_array($admin_arr) && count($admin_arr) ) {
		              	$admin = $admin_arr[0]->toArray();
                                                 
                        $admin_role = new Admin_Model_AdminRole;
                        // находим все роли пользователя
                        $admin_roles_arr = $admin_role->fetchAssoc( 'role_acl_key', 'SELECT r.role_acl_key FROM (`admins_roles`)inner join `role` as r on asrol_role_id=r.role_id  WHERE asrol_admin_id = "' . $admin['admin_id'] . '"' );
					
						
                        foreach ($admin_roles_arr as $v) {
                            $admin_roles[] = $v["role_acl_key"];
                        }

                        //Загружаем роли в класc авторизации
                        //даже если у пользователя нет ролей даём ему роль guests
								
                        if(!isset($admin_roles)){
					   
							$admin_roles = array('guests');
						
                        }
                    
                        K_Auth::authorize($admin, $admin_roles );
											
                        K_Request::redirect( "/admin" );

                    } else {
                        $auth_error = true;
                    }
                } else {
                    $auth_error = true;
                }
            } else {
                $auth_error = true;
            }
        }
        $this->view->error_msg = '';
        if ( $auth_error ) {
            $this->view->error_msg = "Ошибка авторизации, неверный логин или пароль";
        }
      
    }

    public function logoutAction() {
            K_Auth::logout();
            K_Request::redirect("/admin/auth");
    }
}
