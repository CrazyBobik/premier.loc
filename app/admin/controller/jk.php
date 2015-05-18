<?php
class Admin_Controller_Jk extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Новостройки ЖК';
    
        //[%selects-index%]
           $options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM users");
foreach( $res as $t){
$options[$t['mail']] = array('value'=>$t['id']);
};
$this->view->selects->user = $options;

$options = array('Любой'=>array('value'=>''));
$res = k_q::query("SELECT * FROM region");
foreach( $res as $t){
$options[$t['name']] = array('value'=>$t['id']);
};
$this->view->selects->region = $options;


        //[%/selects-index%]
           
        $this->render('jk');  
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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS n.id id,n.date date,u.mail user,r.name region,n.jk_name jk_name,n.img img,n.company_name company_name,n.company_logo company_logo,n.moderation moderation,n.site site,n.text text,n.street street,n.house house,n.sec_num sec_num,n.sec_level sec_level,n.flat_num flat_num,n.parking parking,n.material material,n.code code,n.phone phone,n.email email,n.start_date start_date,n.finish_date finish_date,n.price_from price_from,n.price_to price_to,n.video_link video_link,n.is_complete is_complete  FROM `novostroyki` `n`
                                                                                
																					 LEFT JOIN users u ON u.id=n.user
                                                                                     LEFT JOIN region r ON r.id=n.region
                                                                                                                                                               
                                                                                $where ORDER BY n.id DESC LIMIT $start, $onPage");
        //[%/loadQuery%] 
                
        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
$itemRow["id"] = strip_tags(htmlspecialchars($v["id"]));
$itemRow["date"] = strip_tags(htmlspecialchars($v["date"]));
$itemRow["user"] = !empty($v["user"])? '<a href="//'.$v["user"].'"/>'.$v["user"].'</a>' :"";
$itemRow["region"] = strip_tags(htmlspecialchars($v["region"]));
$itemRow["jk_name"] = strip_tags(htmlspecialchars($v["jk_name"]));
$itemRow["img"] = !empty($v["img"])? '<img width="70" src="/img/novostroyki/'.$v["img"].'"/>' :"";
$itemRow["company_name"] = strip_tags(htmlspecialchars($v["company_name"]));
$itemRow["company_logo"] = !empty($v["company_logo"])? '<img width="70" src="/img/companieslogos/thumb/'.$v["company_logo"].'.jpg"/>' :"";
$itemRow["moderation"] = strip_tags(htmlspecialchars($v["moderation"]));
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
    
        $itemModel = new  Site_Model_Jk;
        
        // LEFT JOIN obj_rooms r ON r.id = o.id
        
        $this->view->item= $itemModel->row("SELECT SQL_CALC_FOUND_ROWS n.id id,n.date date,n.user user,n.region region,n.jk_name jk_name,n.img img,n.company_name company_name,n.company_logo company_logo,n.moderation moderation,n.site site,n.text text,n.street street,n.house house,n.sec_num sec_num,n.sec_level sec_level,n.flat_num flat_num,n.parking parking,n.material material,n.code code,n.phone phone,n.email email,n.start_date start_date,n.finish_date finish_date,n.price_from price_from,n.price_to price_to,n.video_link video_link,n.is_complete is_complete  FROM `novostroyki` `n`
                                                                                
																					 LEFT JOIN users u ON u.id=n.user
                                                                                     LEFT JOIN region r ON r.id=n.region
                                                                                                                                                                                                                                                 
                                                                                WHERE n.id = $id");
                                    
        //[%selects-edit%]                            
            $options = array();
$res = k_q::query("SELECT * FROM users");
foreach( $res as $t){
$options[$t['mail']] = array('value'=>$t['id']);
};
$this->view->selects->user = $options;

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
          
        $itemModel = new Site_Model_Jk;
        
        if ($itemModel->isValidRow($data, $validate)){
          
            if($id){
                 
               $itemModel->update($data, array($this->crudConfig->primary()=>$id));
               
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно обновлёна"; 
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/jk/edit?id='.$id.'"); $("#jk_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){$("#jk_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
            }
      
        } else {
       
            $jsonReturn['error'] = true;
            $jsonReturn['errormsg'] = $itemModel->getErrorsD($lables);
      
        }
        
        $this->putJSON($jsonReturn);
  }

  public function removeAction(){
    
        $itemModel = new Site_Model_Jk;
        $id = intval($_POST[$this->crudConfig->primary()]);
     
        if($id){
            $itemModel->removeID($id);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' => 'Неправильный индитификатор', 'id' => $id));
        }
        
  }
   
}