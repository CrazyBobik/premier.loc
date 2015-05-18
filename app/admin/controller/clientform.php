<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_ClientForm extends Controller {

    public function saveAction() {

        $typeClientForm = new Admin_Model_ClientForm;
        $clientFormKey = $this->getParam('key');

        $clientFormKeyArray = isset($_POST['frmb']) ? $_POST : false;

        if ($clientFormKeyArray != false) {

            K_Loader::load('formbuilder', APP_PATH . '/plugins');
            //сохраняем поля дополнительной формы
            $Xform['type'] = 'xform';
            $Xform['values']['admin_email'] = $_POST['admin_email'];

            $Xform['values']['ck_admin_email'] = $_POST['ck_admin_email'] ? true : false;
            $Xform['values']['ck_client_email'] = $_POST['ck_client_email'] ? true : false;
            $Xform['values']['ck_save_db'] = $_POST['ck_save_db'] ? true : false;

            $Xform['values']['client_email_field_name'] = $_POST['client_email_field_name'];
            $Xform['values']['client_email_ck_name'] = $_POST['client_email_ck_name'];
            $Xform['values']['client_mail_tmp'] = $_POST['client_mail_tmp'];
            $Xform['values']['admin_mail_tmp'] = $_POST['admin_mail_tmp'];
          

            $clientFormKeyArray['frmb'][] = $Xform;

            $form_builder = new Formbuilder($clientFormKeyArray);

            $form_array = $form_builder->get_encoded_form_array();
            //$form_array
            $form_data = array('type_clientform_id' => $clientFormKey, 'type_clientform_content' => serialize($form_array));
            $typeClientForm->save($form_data);
        }
        $this->putAjax('OK');
    }

    public function loadAction() {

        $typeClientForm = new Admin_Model_ClientForm;

        $clientFormKey = $this->getParam('key');

        $clientFormData = $typeClientForm->fetchRow(K_Db_Select::create()->where("type_clientform_id=$clientFormKey"));

        if ($clientFormData) {
            K_Loader::load('formbuilder', APP_PATH . '/plugins');
            $formBuilder = new Formbuilder(unserialize($clientFormData['type_clientform_content']));
            $this->putAjax($formBuilder->render_json());
        } else {
            $this->putAjax('ERROR');
        }
        
    }

    public function delCompletedAction() {
        $clientFormData = new Admin_Model_ClientFormData;
        $delId = $_POST['delid'];
        $clientFormData->removeID($delId);
        $returnJson = array('error' => false, 'msg' => '<strong>OK:</strong>Заполненная форма удалена');
        $this->putJSON($returnJson);
    }
    
    public function loadCompletedFormsAction() {
        $page=intval($_POST['page']);
        $onPage=intval($_POST['onPage']);
        
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
            $formData = Gcontroller::loadclientFormStructure(trim($_POST['tree_link']));

            /*    $clientFormData = $typeClientForm->fetchRow( K_Db_Select::create()->where( "type_clientform_id=$clientFormKey" ) );
            $this->view->formStructure=unserialize( $clientFormData['type_clientform_content'] );*/

            $formStructure = json_decode($formData['form_structure']);
            $formStructure = K_Tree_Types::objectToArray($formStructure);
       
            $fieldCount=0;
                     
          foreach ($formStructure as $v) {
                    if (isset($v['values']['name']) && isset( $v['values']['label'])) {
                      $colsKeys[] = $v['values']['name'];
                      $fieldCount++;  
                    }
              if ($fieldCount>3)break; 
          }
        
        $query = new K_Db_Query();
        $sql = "SELECT * FROM clientform_data WHERE clientform_data_type=".K_Db_Quote::quote(trim($_POST['tree_link']))." ORDER by clientform_data_date DESC LIMIT $start, $onPage";
        
        $formsRes = $query->q($sql);  
              
        $sql ="SELECT count(*) as countItems from clientform_data WHERE clientform_data_type=".K_Db_Quote::quote(trim($_POST['tree_link']));
        $countItems = $query->q($sql);   
        $countItems=$countItems[0]['countItems'];
     
          $forms=array(); 
          foreach($formsRes as $v){
            $formData=array();
            $id=$v['clientform_data_id'];
            $formData[]=$v['clientform_data_date'];
            $data=unserialize($v['clientform_data_content']);
            $data = K_Tree_Types::objectToArray($data);
            
                    foreach($colsKeys as $n){    
                        $formData[] =isset($data[$n])?$data[$n]:'off';   
                    }
            $formData['id']=$id;        
            $forms[]=$formData;
           }
            
           $returnJson = array('error' => false,
                            'items'=>$forms,
                            'countItems'=>$countItems
                           );
  
        $this->putJSON($returnJson);   
   } 
    

    public function completedFormAction(){
        
        $clientFormData = new Admin_Model_ClientFormData;
        $clientFormDataKey = $this->getParam('key');
        $data = $clientFormData->fetchRow(K_Db_Select::create()->where(array('clientform_data_id' => $clientFormDataKey)));

        //вытягиваем структуру формы.
        $formData = Gcontroller::loadclientFormStructure($data['clientform_data_type']);
        $formStructure = json_decode($formData['form_structure']);
        $formStructure = K_Tree_Types::objectToArray($formStructure);

        //выбираем пары имя, label
        foreach ($formStructure as $v) {
            
            if (isset($v['values']['name']) && isset($v['values']['label'])) {
                
                $name = $v['values']['name'];
                $lable = $v['values']['label'];
                $fields[$name] = $lable;
                
            }
            
        }

        if ($data) {
            
            $formDataHtml = '';
            $fromDataObj = unserialize($data['clientform_data_content']);
            
            foreach ($fromDataObj as $k => $v) {
               
                if(is_array($v)){
                  
                    $value = implode(', ', $v);
                    
                }else{
                    
                    $value = $v;
                    
                }
                
                $formDataHtml .= '<tr><td>' . $fields[$k] . '</td><td>' . $value . '</td></tr>';
            }

            $this->putAjax('<table class="table-skeleton">' . $formDataHtml . '</table>');
            
        } else {
            
            $this->putAjax('ERROR');
            
        }
    }
}
