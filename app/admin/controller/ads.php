<?php
class Admin_Controller_Ads extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Объявления';
    
        //[%selects-index%]
           $options = array('Любой'=>array('value'=>''));
		   
$res = k_q::query("SELECT * FROM users ORDER BY name");
foreach( $res as $t){
$options[$t['name'].' '.$t['fam'].' - '.$t['mail']] = array('value'=>$t['id']);
};
$this->view->selects->user = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM transac");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->type_transac = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM ads_sec");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->category = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM ads_subsec");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->type_propert = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM region");
foreach( $res as $t){
$options[$t['name']] = array('value'=>$t['id']);
};
$this->view->selects->region = $options;


        //[%/selects-index%]
      
        /*
        $query = new Dm_Db_Query;
        
        $this->view->types = $query->q('SELECT name, id from obj_types ORDER BY name', true);
      
        $this->view->filials = $query->q('SELECT title, id from obj_branches ORDER BY title', true);
        */
        
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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS a.id id,a.date date,a.pub pub,u.mail user,u.name name,u.fam fam,t.title type_transac,c.title category,p.title type_propert,r.name region  FROM `ads` a
                                                                
                                                                                     LEFT JOIN users u ON u.id=a.user
                                                                                     LEFT JOIN transac t ON t.id=a.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=a.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=a.type_propert
                                                                                     LEFT JOIN region r ON r.id=a.region
                                                                                    
                                                                                $where ORDER BY id DESC LIMIT $start, $onPage");
        //[%/loadQuery%] 
                
        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
				$itemRow["id"]=strip_tags(htmlspecialchars($v["id"]));
				$itemRow["date"]=strip_tags(htmlspecialchars($v["date"]));
				$itemRow["pub"]=strip_tags(htmlspecialchars($v["pub"]));
				$itemRow["user"]='<a href="/admin/users?mail='.strip_tags(htmlspecialchars($v["user"])).'" target="_blank">'.strip_tags(htmlspecialchars($v["user"])).'</a>';
				$itemRow["name"]=strip_tags(htmlspecialchars($v["name"]));
				$itemRow["fam"]=strip_tags(htmlspecialchars($v["fam"]));
				$itemRow["type_transac"]=strip_tags(htmlspecialchars($v["type_transac"]));
				$itemRow["category"]=strip_tags(htmlspecialchars($v["category"]));
				$itemRow["type_propert"]=strip_tags(htmlspecialchars($v["type_propert"]));
				$itemRow["region"]=strip_tags(htmlspecialchars($v["region"]));

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
    
        $itemModel = new  Site_Model_Ads;
        
        // LEFT JOIN obj_rooms r ON r.id = o.id
        
        $this->view->item= $itemModel->row("SELECT a.id id,a.date date,a.pub pub,a.user user,a.type_transac type_transac,a.category category,a.type_propert type_propert,a.region region  FROM `ads` a
                                                                
                                                                                     LEFT JOIN users u ON u.id=a.user
                                                                                     LEFT JOIN transac t ON t.id=a.type_transac
                                                                                     LEFT JOIN ads_sec c ON c.id=a.category
                                                                                     LEFT JOIN ads_subsec p ON p.id=a.type_propert
                                                                                     LEFT JOIN region r ON r.id=a.region
                                                                                     
                                                                                WHERE a.id = $id");
                                    
        //[%selects-edit%]                            
            $res = k_q::query("SELECT * FROM users");
foreach( $res as $t){
$options[$t['mail']] = array('value'=>$t['id']);
};
$this->view->selects->user = $options;
$res = k_q::query("SELECT * FROM transac");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->type_transac = $options;
$res = k_q::query("SELECT * FROM ads_sec");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->category = $options;
$res = k_q::query("SELECT * FROM ads_subsec");
foreach( $res as $t){
$options[$t['title']] = array('value'=>$t['id']);
};
$this->view->selects->type_propert = $options;
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
          
        $itemModel = new Site_Model_Ads;
        
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
    
        $itemModel = new Site_Model_Ads;
        $id = intval($_POST[$this->crudConfig->primary()]);
     
        if($id){
            $itemModel->removeID($id);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' =>'Неправильный индитификатор'));
        }
        
  }
   
}