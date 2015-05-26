<?php
class Admin_Controller_Import extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Выгрузка';
    
        //[%selects-index%]
           
        //[%/selects-index%]
           
        $this->render('import');  
    }

    public function loadAction(){
      
        //[%preaperPage%]
        $page = intval($_POST['page']);
        $onPage = intval($_POST['onPage']);
        
        if ($page) {

            if (! $onPage) {
                $onPage = 10;
            }

            $start = $page * $onPage - $onPage;

        } else {
            
            $start = 0;
            $page = 1;
            $onPage = 10;
            
        }   
              
        $where = K_ws::get($_POST)->fromConfig($this->crudConfig->whereSets());
      
        if ($where && count($where)){
            
            $where = ' WHERE ' . implode(' AND ', $where);
            
        }else{
            
            $where='';
        } 
        //[%/preaperPage%]
        
        //[%loadQuery%]
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS `imp`.`id` `id`,`imp`.`start_date` `start_date`,`imp`.`infeed` `infeed`,`imp`.`processed` `processed`,`imp`.`publicated` `publicated`,`imp`.`updated` `updated`,`imp`.`deleted` `deleted`,`imp`.`errors` `errors`,`imp`.`add_date` `add_date`,`imp`.`limit` `limit`  FROM `import` imp
                      $where ORDER BY id ASC LIMIT $start, $onPage");
        //[%/loadQuery%] 
                
        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
$itemRow["id"] = strip_tags(htmlspecialchars($v["id"]));
$itemRow["start_date"] = strip_tags(htmlspecialchars($v["start_date"]));
$itemRow["infeed"] = strip_tags(htmlspecialchars($v["infeed"]));
$itemRow["processed"] = strip_tags(htmlspecialchars($v["processed"]));
$itemRow["publicated"] = strip_tags(htmlspecialchars($v["publicated"]));
$itemRow["updated"] = strip_tags(htmlspecialchars($v["updated"]));
$itemRow["deleted"] = strip_tags(htmlspecialchars($v["deleted"]));
$itemRow["errors"] = strip_tags(htmlspecialchars($v["errors"]));
$itemRow["add_date"] = strip_tags(htmlspecialchars($v["add_date"]));
$itemRow["limit"] = strip_tags(htmlspecialchars($v["limit"]));

            //[%loadArray%]
            
            $items[] = $itemRow;
            
        }

        $returnJson = array(
            'error' => false,
            'items' => $items,
            'countItems' => $countItems
            );

        $this->putJSON($returnJson);
     }
   
    
     public function editAction(){
        
        $this->disableLayout = true;
        
        $id = intval($_GET['id']); 
    
        $itemModel = new  Site_Model_Import;
        
        // LEFT JOIN obj_rooms r ON r.id = o.id
        
        $this->view->item= $itemModel->row("SELECT SQL_CALC_FOUND_ROWS `imp`.`id` `id`,`imp`.`start_date` `start_date`,`imp`.`infeed` `infeed`,`imp`.`processed` `processed`,`imp`.`publicated` `publicated`,`imp`.`updated` `updated`,`imp`.`deleted` `deleted`,`imp`.`errors` `errors`,`imp`.`add_date` `add_date`,`imp`.`limit` `limit`  FROM `import` imp
                      WHERE imp.id = $id");
                                    
        //[%selects-edit%]                            
                 
        //[%selects-edit%]
  		
		$this->render('edit');
		
    } 
    
    public function saveAction(){
        
        $id = intval($_POST[$this->crudConfig->primary()]);  
   
        $lables = $this->crudConfig->lables();
        
        $data = $this->crudConfig->data($_POST);
              
        $validate = $this->crudConfig->validate();
          
        $itemModel = new Site_Model_Import;
        
        if ($itemModel->isValidRow($data, $validate)){
          
            if($id){
                 
               $itemModel->update($data, array($this->crudConfig->primary()=>$id));
               
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно обновлёна"; 
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/import/edit?id='.$id.'"); $("#import_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){$("#import_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
            }
      
        } else {
       
            $jsonReturn['error'] = true;
            $jsonReturn['errormsg'] = $itemModel->getErrorsD($lables);
      
        }
        
        $this->putJSON($jsonReturn);
  }

  public function removeAction(){
    
        $itemModel = new Site_Model_Import;
        $id = intval($_POST[$this->crudConfig->primary()]);
     
        if($id){
            $itemModel->removeID($id);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' => 'Неправильный индитификатор', 'id' => $id));
        }
        
  }
   
}