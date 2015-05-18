<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_ClientFormTest extends Controller {
    /* {public} */
    public $helpers = array(
        'paginator',
        'call',
        'error',
        'form',
        'include',
        'ru');
    public $formTemplate = array(
        'formStart' => '',
        'formEnd' => '<div style="margin: 0 auto; width: 90%; display: none; opacity: 0.0;" class="nNote nSuccess hideit" id="x_formsuccess_{{formid}}"><p></p></div>',
        'row' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight">{{element}}</div><div class="fix"></div></div>',
        'row_submit' => '{{element}}',
        'row_reset' => '{{element}}',
        'row_file' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'row_select' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'checkbox' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'radio' => '<div class="rowElem noborder admin-form-row"><label>{{label}}:</label><div class="formRight" style="margin-right: 0; width: 662px;">{{element}}</div><div class="fix"></div></div>',
        'row_formbuilder' => '{{element}}',
        );
    protected function indexAction() {
        $this->view->actionType = 'create';
        $this->render('index');
    }


    protected function saveAction() {

         if (! K_Request::isPost()) {
            //ошибка
            $this->putAjax("ERROR");
        }
        $typeClientForm = new Admin_Model_ClientForm;

        //загружаем данные формы
        $formData = Gcontroller::loadclientFormStructure(trim($_POST['tree_link']));

        /*    $clientFormData = $typeClientForm->fetchRow( K_Db_Select::create()->where( "type_clientform_id=$clientFormKey" ) );
        $this->view->formStructure=unserialize( $clientFormData['type_clientform_content'] );*/

        $formStructure = json_decode($formData['form_structure']);
        $formStructure = K_Tree_Types::objectToArray($formStructure);

        foreach ($formStructure as $v) {
            if ($v['type'] == 'xform') {
                //сохраняем дополнительный настройки
                $Xform = $v['values'];
            } else {
                // сохраним ключи полей, что-бы сохранять в базу только то что надо.
                $formFields[] = $v['values']['name'];
                if (isset($v['values']['name']) && isset($v['vlds'])) {
                    $name = $v['values']['name'];
                    $nameAccos[$name] = $v['values']['label'];
                    $vlds = $v['vlds'];
                    $fieldVlds = array();
                    foreach ($vlds as $vld) {
                        if ($vld == "requred") {
                            $fieldVlds['requred'] = true;
                        } else {
                            $fieldVlds[] = $vld;
                        }
                    }
                    $validate[$name] = $fieldVlds;
                }
            }
        }

        // выбираем из поста только нужные поля
        foreach ($_POST as $k => $v) {
            if (in_array($k, $formFields)) {
                
                if(is_string($v)){
                  $data[$k] = trim($v);
                }
               $data[$k]=$v;
             }
        }

        if ($typeClientForm->isValidRow($data, $validate)) {
            $clientFormData = new Admin_Model_ClientFormData;

            $saveDate = array('clientform_data_type' => trim($_POST['tree_link']), 'clientform_data_content' => serialize($data));

            // сахроняем форму и отправляем письма.
            if ($Xform['ck_save_db']) {
                $clientFormData->save($saveDate);
            }

            $render = new K_TemplateRender();
            $render->setTags($data);
            $mailer = new K_Mail;
            if (isset($Xform['ck_admin_email']) && $Xform['admin_mail_tmp'] && $Xform['admin_email']) {
                //Отправляем письмо на емеил админа
                $mailText = $render->assemble($Xform['admin_mail_tmp']);
                $mailer->setBody($mailText);
                $mailer->addTo($Xform['admin_email']);
                $mailer->send('test@ukr.net', 'Ползователь заполнил форму');
           }
           
       //  echo $data['ck_client_email'].'    '.$Xform['ck_client_email'].' '.$Xform['client_email_field_name'].'  '.$Xform['client_mail_tmp'];
        
                if (isset($Xform['client_email_ck_name']) && $Xform['client_email_ck_name']){
                    
                  $clientEmailCkName=$Xform['client_email_ck_name'];  
                    
                }
        
        // echo $data[$clientEmailCkName].'    '.$Xform['ck_client_email'].' '.$Xform['client_email_field_name'].'  '.$Xform['client_mail_tmp'];
       
        
          if (isset($data[$clientEmailCkName]) && isset($Xform['ck_client_email']) && isset($Xform['client_email_field_name']) && isset($Xform['client_mail_tmp'])) {
                $clientEmailFieldName = $Xform['client_email_field_name'];
                
               if(isset($data[$clientEmailFieldName])) {
                     //Отправляем письмо на емеил пользователя
              
                    $mailText = $render->assemble($Xform['client_mail_tmp']);
                    $mailer->setBody($mailText);       
                    $mailer->addTo($data[$clientEmailFieldName]);
                    $mailer->send('test@ukr.net','Ваша форма удачно отправленна');
                }
           }
            $jsonReturn['error'] = false;
            $jsonReturn['msg'] = '<strong>ОК:<strong> Форма удачно отправлена';
        } else {
            $jsonReturn['error'] = true;
            $jsonReturn['msg'] = $typeClientForm->getErrorsD($nameAccos);
        }
        
        if (K_Request::isAjax()) {
            $this->putJSON($jsonReturn);
        } else {
            $this->putAjax("ERROR");
        }

        //
        /*else{
        if($jsonReturn['error'] = false){
        
        //заготовка на случай если js отключен
        //загрузка промежуточного шаблона с выводом ошибок и формой для продолжения заполнения 
        
        
        }  
        else{
        // промежуточный шаблон с нотификацией о правильном заполнении и редирект туда от куда пришол пользователь.    
        
        
        }
        
        }*/
    }
}
