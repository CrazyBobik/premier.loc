<?php
class Admin_Controller_Ads extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Обекты';
    
        //[%selects-index%]
           
        //[%/selects-index%]
           
        $this->render('ads');  
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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS a.id id  FROM `ads` a

                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      $where ORDER BY id ASC LIMIT $start, $onPage");
        //[%/loadQuery%] 
                
        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
$itemRow["id"] = strip_tags(htmlspecialchars($v["id"]));

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
    
        $itemModel = new  Site_Model_Objects;
        
        // LEFT JOIN obj_rooms r ON r.id = o.id
        
        $this->view->item= $itemModel->row("SELECT a.id id  FROM `ads` a
                                                                
                                                                                     LEFT JOIN users u ON u.id=a.user
                                                                                     LEFT JOIN transac t ON t.id=a.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=a.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=a.type_propert
                                                                                     LEFT JOIN region r ON r.id=a.region
                                                                                     
                                                                                WHERE a.id = $id");
                                    
        //[%selects-edit%]                            
                 
        //[%selects-edit%]
  		
		$this->render('edit');
		
    } 
    
    public function saveAction(){
        
        $id = intval($_POST[$this->crudConfig->primary()]);  
   
        $lables = $this->crudConfig->lables();
        
        $data = $this->crudConfig->data($_POST);
              
        $validate = $this->crudConfig->validate();
          
        $itemModel = new Site_Model_Objects;
        
        if ($itemModel->isValidRow($data, $validate)){
          
            if($id){
                 
               $itemModel->update($data, array($this->crudConfig->primary()=>$id));
               
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно обновлёна"; 
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/ads/edit?id='.$id.'"); $("#ads_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){$("#ads_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
            }
      
        } else {
       
            $jsonReturn['error'] = true;
            $jsonReturn['errormsg'] = $itemModel->getErrorsD($lables);
      
        }
        
        $this->putJSON($jsonReturn);
  }

  public function removeAction(){
    
        $itemModel = new Site_Model_Objects;
        $id = intval($_POST[$this->crudConfig->primary()]);
     
        if($id){
            $itemModel->removeID($id);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' => 'Неправильный индитификатор', 'id' => $id));
        }
        
  }
   
}