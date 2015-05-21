<?php
class Admin_Controller_Objects extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Обекты';
    
        //[%selects-index%]
           $options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM type_country");
foreach( $res as $t){
$options[$t['type_country_name']] = array('value'=>$t['type_country_id']);
};
$this->view->selects->country = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM type_region");
foreach( $res as $t){
$options[$t['type_region_name']] = array('value'=>$t['type_region_id']);
};
$this->view->selects->category = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM type_typejk");
foreach( $res as $t){
$options[$t['type_typejk_name']] = array('value'=>$t['type_typejk_id']);
};
$this->view->selects->type = $options;


        //[%/selects-index%]
           
        $this->render('objects');  
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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name category,jk.type_typejk_id type,a.area area  FROM `objects` a

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
$itemRow["country"] = strip_tags(htmlspecialchars($v["country"]));
$itemRow["category"] = strip_tags(htmlspecialchars($v["category"]));
$itemRow["type"] = strip_tags(htmlspecialchars($v["type"]));
$itemRow["area"] = strip_tags(htmlspecialchars($v["area"]));

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
        
        $this->view->item= $itemModel->row("SELECT a.id id,a.country country,a.category category,a.type type,a.area area  FROM `objects` a
                                                                
                                                                                     LEFT JOIN users u ON u.id=a.user
                                                                                     LEFT JOIN transac t ON t.id=a.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=a.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=a.type_propert
                                                                                     LEFT JOIN region r ON r.id=a.region
                                                                                     
                                                                                WHERE a.id = $id");
                                    
        //[%selects-edit%]                            
            $options = array();
$res = k_q::query("SELECT * FROM type_country");
foreach( $res as $t){
$options[$t['type_country_name']] = array('value'=>$t['type_country_id']);
};
$this->view->selects->country = $options;

$options = array();
$res = k_q::query("SELECT * FROM type_region");
foreach( $res as $t){
$options[$t['type_region_name']] = array('value'=>$t['type_region_id']);
};
$this->view->selects->category = $options;

$options = array();
$res = k_q::query("SELECT * FROM type_typejk");
foreach( $res as $t){
$options[$t['type_typejk_name']] = array('value'=>$t['type_typejk_id']);
};
$this->view->selects->type = $options;

     
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
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/objects/edit?id='.$id.'"); $("#objects_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){$("#objects_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
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