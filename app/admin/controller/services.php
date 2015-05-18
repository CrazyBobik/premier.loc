<?php
class Admin_Controller_Services extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Компании';
    
        //[%selects-index%]
           $options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM users");
foreach( $res as $t){
$options[$t['mail']] = array('value'=>$t['id']);
};
$this->view->selects->user = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM services_list");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->category = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM region");
foreach( $res as $t){
$options[$t['name']] = array('value'=>$t['id']);
};
$this->view->selects->region = $options;


        //[%/selects-index%]
           
        $this->render('services');  
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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS s.id id,s.date date,u.mail user,l.title category,r.name region,s.logo logo,s.moderation moderation,s.title title,s.site site,s.text text  FROM `services_cont` `s`
                                                                                
																					 LEFT JOIN users u ON u.id=s.user
                                                                                     LEFT JOIN region r ON r.id=s.region
                                                                                     LEFT JOIN services t ON t.id=s.type
                                                                                     LEFT JOIN services_list l ON l.id=s.category
                                                                                
                                                                                $where ORDER BY s.id DESC LIMIT $start, $onPage");
        //[%/loadQuery%] 
                
        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
$itemRow["id"] = strip_tags(htmlspecialchars($v["id"]));
$itemRow["date"] = strip_tags(htmlspecialchars($v["date"]));
$itemRow["user"] = !empty($v["user"])? '<a href="//'.$v["user"].'"/>'.$v["user"].'</a>' :"";
$itemRow["category"] = strip_tags(htmlspecialchars($v["category"]));
$itemRow["region"] = strip_tags(htmlspecialchars($v["region"]));
$itemRow["logo"] = !empty($v["logo"])? '<img width="70" src="/img/services/thumb/'.$v["logo"].'"/>' :"";
$itemRow["moderation"] = strip_tags(htmlspecialchars($v["moderation"]));
$itemRow["title"] = strip_tags(htmlspecialchars($v["title"]));
$itemRow["site"] = strip_tags(htmlspecialchars($v["site"]));
$itemRow["text"] = k_string::trunc(htmlspecialchars($v["text"]),150,true);

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
    
        $itemModel = new  Site_Model_Service;
        
        // LEFT JOIN obj_rooms r ON r.id = o.id
        
        $this->view->item= $itemModel->row("SELECT SQL_CALC_FOUND_ROWS s.id id,s.date date,s.user user,s.category category,s.region region,s.logo logo,s.moderation moderation,s.title title,s.site site,s.text text  FROM `services_cont` `s`
                                                                                
																					 LEFT JOIN users u ON u.id=s.user
                                                                                     LEFT JOIN region r ON r.id=s.region
                                                                                     LEFT JOIN services t ON t.id=s.type
                                                                                     LEFT JOIN services_list l ON l.id=s.category
                                                                                                                                                                
                                                                                WHERE s.id = $id");
                                    
        //[%selects-edit%]                            
            $options = array();
$res = k_q::query("SELECT * FROM users");
foreach( $res as $t){
$options[$t['mail']] = array('value'=>$t['id']);
};
$this->view->selects->user = $options;

$options = array();
$res = k_q::query("SELECT * FROM services_list");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->category = $options;

$options = array();
$res = k_q::query("SELECT * FROM region");
foreach( $res as $t){
$options[$t['name']] = array('value'=>$t['id']);
};
$this->view->selects->region = $options;

     
        //[%selects-edit%]
  		
		$this->render('edit');
		
    } 
    
    public function saveAction(){
        
        $id = intval($_POST[$this->crudConfig->primary()]);  
   
        $lables = $this->crudConfig->lables();
        
        $data = $this->crudConfig->data($_POST);
              
        $validate = $this->crudConfig->validate();
          
        $itemModel = new Site_Model_Service;
        
        if ($itemModel->isValidRow($data, $validate)){
          
            if($id){
                 
               $itemModel->update($data, array($this->crudConfig->primary()=>$id));
               
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно обновлёна"; 
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/services/edit?id='.$id.'"); $("#services_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){$("#services_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
            }
      
        } else {
       
            $jsonReturn['error'] = true;
            $jsonReturn['errormsg'] = $itemModel->getErrorsD($lables);
      
        }
        
        $this->putJSON($jsonReturn);
  }

  public function removeAction(){
    
        $itemModel = new Site_Model_Service;
        $id = intval($_POST[$this->crudConfig->primary()]);
     
        if($id){
            $itemModel->removeID($id);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' =>'Неправильный индитификатор'));
        }
        
  }
   
}