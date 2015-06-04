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
        $this->view->selects->region = $options;

        $options = array('Любой'=>array('value'=>''));
        $res = k_q::query("SELECT * FROM type_city");
        foreach( $res as $t){
            $options[$t['type_city_name']] = array('value'=>$t['type_city_id']);
        };
        $this->view->selects->city = $options;

        $options = array('Любой'=>array('value'=>''));
        $res = k_q::query("SELECT * FROM market");
        foreach( $res as $t){
            $options[$t['name']] = array('value'=>$t['id']);
        };
        $this->view->selects->market = $options;

        $options = array('Любой'=>array('value'=>''));
        $res = k_q::query("SELECT * FROM type_typejk");
        foreach( $res as $t){
            $options[$t['type_typejk_name']] = array('value'=>$t['type_typejk_id']);
        };
        $this->view->selects->type = $options;
        $this->view->types = $res;

        $options = array('Любой'=>array('value'=>''));
        $res = k_q::query("SELECT * FROM currency");
        foreach( $res as $t){
            $options[$t['name']] = array('value'=>$t['id']);
        };
        $this->view->selects->cur = $options;

        $options = array('Любой'=>array('value'=>''));
        $res = k_q::query("SELECT * FROM state");
        foreach( $res as $t){
            $options[$t['name']] = array('value'=>$t['id']);
        };
        $this->view->selects->state = $options;


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
	    $itemsRes = K_q::query("SELECT SQL_CALC_FOUND_ROWS a.id id,cunt.type_country_name country,r.type_region_name region,ci.type_city_name city,m.name market,jk.type_typejk_name type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,cu.name cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,s.name state  FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      $where ORDER BY id ASC LIMIT $start, $onPage");
        //[%/loadQuery%]


        $countItems = K_q::one("SELECT FOUND_ROWS() as countItems;",'countItems');
     
        $items = array();
     
        foreach ($itemsRes as $v){
            
            //[%loadArray%]
                $itemRow=array();
$itemRow["id"] = strip_tags(htmlspecialchars($v["id"]));
$itemRow["country"] = strip_tags(htmlspecialchars($v["country"]));
$itemRow["region"] = strip_tags(htmlspecialchars($v["region"]));
$itemRow["city"] = strip_tags(htmlspecialchars($v["city"]));
$itemRow["market"] = strip_tags(htmlspecialchars($v["market"]));
$itemRow["type"] = strip_tags(htmlspecialchars($v["type"]));
$itemRow["area"] = strip_tags(htmlspecialchars($v["area"]));
$itemRow["all_sq"] = strip_tags(htmlspecialchars($v["all_sq"]));
$itemRow["living_sq"] = strip_tags(htmlspecialchars($v["living_sq"]));
$itemRow["kithcen_sq"] = strip_tags(htmlspecialchars($v["kithcen_sq"]));
$itemRow["price"] = strip_tags(htmlspecialchars($v["price"]));
$itemRow["cur"] = strip_tags(htmlspecialchars($v["cur"]));
$itemRow["to_sea"] = strip_tags(htmlspecialchars($v["to_sea"]));
$itemRow["to_airport"] = strip_tags(htmlspecialchars($v["to_airport"]));
$itemRow["rooms"] = strip_tags(htmlspecialchars($v["rooms"]));
$itemRow["floor"] = strip_tags(htmlspecialchars($v["floor"]));
$itemRow["all_floors"] = strip_tags(htmlspecialchars($v["all_floors"]));
$itemRow["bath_rooms"] = strip_tags(htmlspecialchars($v["bath_rooms"]));
$itemRow["state"] = strip_tags(htmlspecialchars($v["state"]));

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
        
        $this->view->item= $itemModel->row("SELECT a.id_add id_add,a.id id,a.country country,a.region region,a.city city,a.market market,a.type type,a.area area,a.all_sq all_sq,a.living_sq living_sq,a.kithcen_sq kithcen_sq,a.price price,a.cur cur,a.to_sea to_sea,a.to_airport to_airport,a.rooms rooms,a.floor floor,a.all_floors all_floors,a.bath_rooms bath_rooms,a.state state, a.description des  FROM `objects` a
                      LEFT JOIN type_country cunt ON cunt.type_country_id=a.country
                      LEFT JOIN type_region r ON r.type_region_id=a.region
                      LEFT JOIN type_city ci ON ci.type_city_id=a.city
                      LEFT JOIN type_typejk jk ON jk.type_typejk_id=a.type
                      LEFT JOIN market m ON m.id=a.market
                      LEFT JOIN currency cu ON cu.id=a.cur
                      LEFT JOIN state s ON s.id=a.state
                      WHERE a.id = $id");

         $this->view->objimg = K_Q::data('SELECT * FROM objects_img WHERE id_add='.$this->view->item['id_add']);
//    var_dump('SELECT * FROM object_img WHERE id_add='.$this->view->item['id_add']);
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
         $this->view->selects->region = $options;

         $options = array();
         $res = k_q::query("SELECT * FROM type_city");
         foreach( $res as $t){
             $options[$t['type_city_name']] = array('value'=>$t['type_city_id']);
         };
         $this->view->selects->city = $options;

         $options = array();
         $res = k_q::query("SELECT * FROM market");
         foreach( $res as $t){
             $options[$t['name']] = array('value'=>$t['id']);
         };
         $this->view->selects->market = $options;

         $options = array();
         $res = k_q::query("SELECT * FROM type_typejk");
         foreach( $res as $t){
             $options[$t['type_typejk_name']] = array('value'=>$t['type_typejk_id']);
         };
         $this->view->selects->type = $options;

         $options = array();
         $res = k_q::query("SELECT * FROM currency");
         foreach( $res as $t){
             $options[$t['name']] = array('value'=>$t['id']);
         };
         $this->view->selects->cur = $options;

         $options = array();
         $res = k_q::query("SELECT * FROM state");
         foreach( $res as $t){
             $options[$t['name']] = array('value'=>$t['id']);
         };
         $this->view->selects->state = $options;

     
         //[%selects-edit%]
  		
         $this->render('edit');
		
     }
    
    public function saveAction(){
        
        $id = intval($_POST[$this->crudConfig->primary()]);  
   
        $lables = $this->crudConfig->lables();
        
        $data = $this->crudConfig->data($_POST);
        $data['description'] = $_POST['description'];

        $validate = $this->crudConfig->validate();
          
        $itemModel = new Site_Model_Objects;
        
        if ($itemModel->isValidRow($data, $validate)){
          
            if($id){
                 
                $itemModel->update($data, array($this->crudConfig->primary()=>$id));
               
                $jsonReturn['error'] = false;
                $jsonReturn['msg'] = "Запись успешно обновлёна";
                $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/objects/edit?id='.$id.'"); setTimeout(setObjectForm,500); $("#objects_table_wrapper").ajaxLeaf().reload()},1500);}';
               $jsonReturn['clean'] = false;
               
            }else{
                
               $id = $itemModel->save($data);
               $jsonReturn['error'] = false;
               $jsonReturn['msg'] = "Запись успешно добавленна";
               $jsonReturn['callback'] = 'function callback(){setTimeout(function(){reloadFlyForm("/admin/objects/edit?id='.$id.'"); setTimeout(setObjectForm,500);$("#objects_table_wrapper").ajaxLeaf().reload()},500);}';
            
          
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

  public function imageaddAction(){

        $itemModel = new Site_Model_Objects();
        $id_add = intval($_POST['id_add']);

        $imgid = intval($_POST['img_id']);

      $photoKey = 'images_f';

        $rows = K_Q::row('SELECT * FROM objects_img  WHERE id='.$imgid);

        if($imgid && $rows){

            $photoNow = $rows['img'];
            $update = true;

        }else{
            $update = false;
        }

        $form = new K_Form();

        $originalDir = AllConfig::$objImgPaths['original'];

        if ($form->hasFiles()){

            $files = $form->getFiles();

            $exArr = explode('/' . $files[$photoKey ]["type"]);

            $temppi = pathinfo($files[$photoKey ]['name']);

            if ($files[$photoKey ] && $exArr[0] = 'image' &&  in_array($temppi['extension'], AdminConfig::$objectImgType)) {

                $pathData = $form->moveUploadedFile($photoKey , $originalDir , uniqid(), false);

                if ($pathData) {

                    if ($update) {
                        $newImgName = $photoNow;
                    } else {
                        $newImgName = uniqid().'.jpg';
                    }

                    $itemModel->genImages($originalDir.strtolower($pathData['filename']), $newImgName);

                    if(!$update) {
                        K_Q::data('INSERT INTO objects_img (id_add, img) VALUES ('.$id_add.',"'.$newImgName.'")');

                        $imgid = K_Q::lastId();
                    }

                    $thumbSrc='/upload/objects/thumb/'.$newImgName;

                    $returnAjax =<<<HTML

                                       <div style="margin:15px 0" id="img_{$imgid}">

                                            <form action="/admin/objects/imageadd" class="image-update" method="POST" enctype="multipart/form-data">

														<img src="{$thumbSrc}" class="rounded" width="100"/>

														<input style="width:220px" class="object_images" id="object_images_f_0" type="file" name="images_f">

														<input type="hidden" name="img_id" value="$imgid">

														<input type="hidden" name="id_add" value="$id_add">

														<button class="file_field_update update-image" href="javascript:void(0);" title="Обновить"></button>

														<a data-image="{$imgid}" class="file_field_delete remove-image" href="javascript:void(0);" title="Удалить"></a><br/>
                                           </form>

                                        </div>
HTML;



                }else{

                    $returnAjax = "ERROR123:Ошибка загруки изображения";

                }

            }else{
                $returnAjax = "ERROR123:Неправильное расширение картинки, доступны jpg, png, jpeg, gif";
            }

        }else{

            $returnAjax = "ERROR123:Не выбрано изображение для загрузки";

        }

        $this->putAjax($returnAjax);
    }

    public function imageremoveAction(){

        $obj = new Site_Model_Objects();

        $obj->deleteGallImages(array($_POST['image']));

        $this->putJSON(array(
           'error' => false,
            'image' => $_POST['image']
        ));


    }

    public function changeprioritetAction(){

        $objectModel = new Admin_Model_Object;
        $objectId = intval($this->getParam('id'));
        $prioritet = $_POST['param'];

        if ($prioritet && $objectId) {
            $objectModel->update(array('prioritet' => $prioritet),array('id' => $objectId));
            $this->putAjax("OK");
        } else {
            $this->putAjax("ERROR");
        }

    }
}