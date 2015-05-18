<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Dev_Controller_Settings extends Controller {

     public function onInit() {
        $this->formDictionary = new K_Dictionary();
        $this->formDictionary->loadFromIni(ROOT_PATH . '/configs/forms/errors.txt');
        K_Validator::setDefaultDictionary($this->formDictionary);
    }
  
    /* {public} */
    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru');

    private $dictionary = array(
        'user_email' => 'Ваша почта',
        'password1' => 'Новый пароль',
        'password2' => 'Повторите пароль',
        'user_password' => 'Действующий пароль');

    public function indexAction() {
        $this->view->title = 'Личные настройки';
        $this->view->header = 'Личные настройки';
        $this->render('index');
    }

    public function saveAction() {

        if (! K_Request::isPost()) {
            $this->putAjax('ERROR');
        }

        if (! K_Auth::isLogin()) {
            $this->putAjax('ERROR');
        }

        $validate = array('user_password' => array('required' => true, 'userTruePass'), 'user_email' => array(
                'required' => true,
                'lengthTest',
                'email',
                'userExists'));


        $userSettings = new Dev_Model_UserSettings;
        $oldPassword = K_Arr::get($_POST, 'oldpassword', '');

        $data = array(
            'user_password' => trim($_POST['user_password']),
            'user_email' => trim($_POST['user_email']),
            'password1' => trim($_POST['password1']),
            'password2' => trim($_POST['password2']));

        if (strlen($data['password1']) > 0 || strlen($data['password2']) > 0) {
            $validate['password1'] = array('required' => true, 'pwdTest');
        }

        if ($userSettings->isValidRow($data, $validate)) {
            unset($data['user_password']);
            if (strlen($data['password1']) > 0) {
                $data['user_password'] = md5(md5($data['password1'] . K_Registry::get('Configure.salt')));
            }
            unset($data['password1']);
            unset($data['password2']);

          /*  if (! strlen($data['user_email']) > 0) {
                unset($data['user_email']);
            }*/

            if (count($data)) {
                $data['user_id'] = K_Auth::getUserInfo('user_id');
                $userSettings->save($data);
                K_Auth::mergeUserInfo($data);
            }

            $returnJson['error'] = false;
            $returnJson['msg'] = "<strong>OK:</strong>Настройки удачно сохранены";

        } else {
            $returnJson['error'] = true;
            $returnJson['msg'] = $userSettings->getErrorsD($this->dictionary);
        }
        $this->putJSON($returnJson);
    }
}
