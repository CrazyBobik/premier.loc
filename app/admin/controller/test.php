<?php

defined( 'K_PATH' ) or die( 'DIRECT ACCESS IS NOT ALLOWED' );

class Admin_Controller_Test extends Controller{
    
    protected function indexAction(){
        
   	    $this->view->title = 'Тестовый раздел';
        $this->view->headers = array(
                                    array('title' => 'Тестовый раздел'
                                         )
                                   );
                                   
        $testModel = new Admin_Model_Test;                           
          
        /// Example mfa - выбор списка записей элементов 
          
        $this->view->mfa = $testModel->mfa( select()-> where(array( 'test_field' =>'1test_f' )));
        
        /// Example mfo - выбор одного элемента из базы записей, необходимо обязательно указать название поля в селекте
         
        $this->view->mfo = $testModel->mfo( select('test_id')->where(array('test_field2' =>'3test_f2')));
        
        /// Example mfs - выбор одного элемента из базы записей, необходимо обязательно указать название поля в селекте
        
        $this->view->mfs = $testModel->mfs( select('test_field')->where(array('test_field2' =>'4test_f2')));
        
        /// Example mfm - выборка fetchMap
         /** @param $keyField - поле ключа 
          *  @param $valueField - поле значения
          *  @param $sql - условие выборки 
          *  @param $count - количество
          *  @param $keyPrintFormat - формат записи ключа
          *  @return array['$keyField'] = $valueField    
          */
                 
        $this->view->mfm = $testModel->mfm(select()->where());
        
        /// выборка количества записей c условием
        
        $this->view->count = $testModel->count(select()->where());
             
        /// Example save - сохранение или обновление записи 
         
        // $testModel->save( select()->where() );
        
        $this->render( 'test' );        
    }
}
