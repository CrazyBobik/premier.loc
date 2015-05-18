<?php

defined( 'K_PATH' ) or die( 'DIRECT ACCESS IS NOT ALLOWED' );

class Dev_Controller_Auth extends Controller {

    /* {public} */
    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru' );
    public $layout = 'auth_layout-glassok';

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

                    $user_model = new Dev_Model_User;
                    $user_arr = $user_model->find( K_Db_Select::create()->fields( 'user_id, user_name, user_login , user_email' )->where( array( 'and' => array( 'user_login' => $login, "user_password" => $password_hash ) ) )->limit(1) );
 
                 	if ( is_array($user_arr) && count($user_arr) ) {
		              	$user = $user_arr[0]->toArray();
                                                 
                        $user_role = new Dev_Model_UserRole;
                        // находим все роли пользователя
                        $user_roles_arr = $user_role->fetchAssoc( 'role_acl_key', 'SELECT r.role_acl_key FROM (`users_roles`)inner join `role` as r on usrol_role_id=r.role_id  WHERE usrol_user_id = "' . $user['user_id'] . '"' );

                        foreach ($user_roles_arr as $v) {
                            $user_roles[] = $v["role_acl_key"];
                        }

                       //Загружаем роли в класc авторизации
                       // var_dump ($user);
                       //даже если у пользователя нет ролей даём ему роль guests
                       if(!isset($user_roles)){
					   
                        $user_roles=array('guests');
						
                       }
                       
                        K_Auth::authorize($user, $user_roles );
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
