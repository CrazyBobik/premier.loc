<?php
class Admin_Controller_Users extends K_Controller_Admin {
  
    protected function indexAction(){
      
   	    $this->view->title = 'Редактирование пользователей';
    
        //[%selects-index%]
           $options = array('Любой'=>array('value'=>''));
		   $res = k_q::query("SELECT * FROM pkt");
			foreach( $res as $t){
				$options[$t['title']] = array('value'=>$t['id']);
			};
			$this->view->selects->pkt = $options;


        //[%/selects-index%]
      
        /*
        $query = new Dm_Db_Query;
        
        $this->view->types = $query->q('SELECT name, id from obj_types ORDER BY name', true);
      
        $this->view->filials = $query->q('SELECT title, id from obj_branches ORDER BY title', true);
        */
        
        $this->render('users');  
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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS u.id id,u.mail mail,u.name name,u.fam fam,u.colpub colpub,u.colpub_all colpub_all,u.balans balans,p.title pkt,u.avatar avatar,u.date date,
												
																				(SELECT id FROM ads WHERE ads.user=u.id LIMIT 1) as ads_id ,
																				(SELECT id FROM services_cont WHERE services_cont.user=u.id LIMIT 1) as serv_id,
																				(SELECT id FROM history WHERE history.user=u.id and incoming=1 LIMIT 1) as history
																				
																				FROM `users` u
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                $where ORDER BY date DESC  LIMIT $start, $onPage");
																				
        /*var_dump ("SELECT SQL_CALC_FOUND_ROWS u.id id,u.mail mail,u.name name,u.fam fam,u.colpub colpub,u.colpub_all colpub_all,u.balans balans,p.title pkt,u.avatar avatar,u.date date  FROM `users` u
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                LEFT JOIN services_cont sercont ON sercont.user=id
                                                                                $where ORDER BY date DESC  LIMIT $start, $onPage");   */                                                                     
        //[%/loadQuery%] 
                
        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
				$itemRow["id"]=strip_tags(htmlspecialchars($v["id"]));
				$itemRow["mail"]=strip_tags(htmlspecialchars($v["mail"]));
				$itemRow["name"]=strip_tags(htmlspecialchars($v["name"]));
				$itemRow["fam"]=strip_tags(htmlspecialchars($v["fam"]));
				$itemRow["colpub"]=strip_tags(htmlspecialchars($v["colpub"]));
				$itemRow["colpub_all"]=strip_tags(htmlspecialchars($v["colpub_all"]));
				$itemRow["balans"]=strip_tags(htmlspecialchars($v["balans"]));
				$itemRow["pkt"]=strip_tags(htmlspecialchars($v["pkt"]));
				$itemRow["avatar"] = !empty($v["avatar"])? '<img height="40" src="/img/avatars/'.$v["avatar"].'"/>' :"";
				$itemRow["date"]=strip_tags(htmlspecialchars($v["date"]));
				$itemRow["serv_id"]=strip_tags(htmlspecialchars($v["serv_id"]));
				$itemRow["ads_id"]=strip_tags(htmlspecialchars($v["ads_id"]));
				$itemRow["history"]=strip_tags(htmlspecialchars($v["history"]));
				
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
    
        $itemModel = new  Site_Model_User;
        
        // LEFT JOIN obj_rooms r ON r.id = o.id
        
        $this->view->item= $itemModel->row("SELECT SQL_CALC_FOUND_ROWS u.id id,u.mail mail,u.name name,u.fam fam,u.colpub colpub,u.colpub_all colpub_all,u.balans balans,u.pkt pkt,u.avatar avatar,u.date date  FROM `users` u
                                                                                LEFT JOIN pkt p ON u.pkt=p.id
                                                                                WHERE u.id = $id");
                                    
        //[%selects-edit%]   
		
            $res = k_q::query("SELECT * FROM pkt");
			
			foreach( $res as $t){
			
				$options[$t['title']] = array('value'=>$t['id']);
				
			};
			
			$this->view->selects->pkt = $options;
     
        //[%selects-edit%]
  		
		$this->render('edit');
		
    } 
    
    public function saveAction(){
        
        $id = intval($_POST[$this->crudConfig->primary()]);  
   
        $lables = $this->crudConfig->lables();
        
        $data = $this->crudConfig->data($_POST);
              
        $validate = $this->crudConfig->validate();
          
        $itemModel = new Site_Model_User;
        
        if ($itemModel->isValidRow($data, $validate)){
          
            if($id){
                 
               $itemModel->update($data, array($this->crudConfig->primary()=>$id));
               
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно обновлёна"; 
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/users/edit?id='.$id.'"); $("#users_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){$("#users_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
            }
      
        } else {
       
            $jsonReturn['error'] = true;
            $jsonReturn['errormsg'] = $itemModel->getErrorsD($lables);
      
        }
        
        $this->putJSON($jsonReturn);
  }

  public function removeAction(){
    
        $itemModel = new Site_Model_User;
        $id = intval($_POST[$this->crudConfig->primary()]);
     
        if($id){
            $itemModel->removeID($id);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' =>'Неправильный индитификатор'));
        }
        
  }
    
}