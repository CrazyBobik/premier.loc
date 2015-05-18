<?php

defined( 'K_PATH' ) or die( 'DIRECT ACCESS IS NOT ALLOWED' );

class Admin_Controller_Blogs extends Controller {

    public function moveAction() {
     $result = K_Request::call(array( 
                                              'module'     => 'admin',
                                              'controller' => 'tree',
                                              'action'     => 'move',
                                              'params'     => array()
                                              )
                                            );  
       json_decode($result,true);
       if($result['status']){
          $jsonReturn['error'] = false ;
          $jsonReturn['msg'] ='Блог успешно перемещен.';
          $jsonReturn['form'] = false;    
       }
       else{
          $jsonReturn['error'] = true ;
          $jsonReturn['msg'] =array('1'=>array('label'=>'Раздел','error'=>'неправильнный раздел для новости'));    
       }
       $this->putJSON($jsonReturn);
    }
    
    protected function indexAction() {
   	    $this->view->title = 'Просмотр новостей';
        $this->view->headers = array(
                                    array('title' => 'Просмотр новостей'
                                          ),
                                    array('title' => 'Обычная новость',
                                            'href' => '/admin/addnew/'
                                        ),
                                    array( 'title' => 'Мультиязычная новость',
                                            'href' => '/admin/addmultinew')
                                    );
                                    
        $this->view->sections = K_TreeQuery::crt('/news/')->types('section')->go();                            
        $this->render('news');  
    }

    public function loadAction() {
      
        $page = intval($_POST['page']);
        $onPage = intval($_POST['onPage']);
 
        $searche = $_POST['filter'];
        $newId = intval($_POST['newid']);
        
        if ($dateStart = K_Date::dateParse($_POST['date-start'])) {
            $dateStart =  mktime(0,0,0, $dateStart['m'], $dateStart['d'], $dateStart['y']);
        }
        if ($dateStop = K_Date::dateParse($_POST['date-stop'])) {
            $dateStop =  mktime(23,59,59, $dateStop['m'], $dateStop['d'], $dateStop['y']);
        }
      
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
        
        if ($dateStart && $dateStop) {
            if ($dateStart > $dateStop) {
                $where[] ="UNIX_TIMESTAMP(type_news_date) >= ".K_Db_Quote::quote($dateStart);
            } else {
                 $where[] ="(UNIX_TIMESTAMP(type_news_date) BETWEEN  ".K_Db_Quote::quote($dateStart)." AND ".K_Db_Quote::quote($dateStop).")";
            }
        } else
        if ($dateStart) {
            $where[] ="UNIX_TIMESTAMP(type_news_date) >= ".K_Db_Quote::quote($dateStart);
        } else
        if ($dateStop) {
            $where[] ="UNIX_TIMESTAMP(type_news_date) <= ".K_Db_Quote::quote($dateStop);
        }
        
        if($_POST['news-lang']){
            $where[] = " type_news_lang = " . K_Db_Quote::quote($_POST['news-lang']);
        }
        
         if($section=intval($_POST['section'])){
            $where[] = " tree_pid = " . K_Db_Quote::quote($section);
        }
   
   if($searche && mb_strlen($searche)>2){
        if($searche){
            $where[] = "(type_news_title LIKE " . K_Db_Quote::quote('%'.$searche.'%').' OR type_news_author LIKE ' . K_Db_Quote::quote($searche.'%').")";
         }
   }
   
        if ($where && count($where)) {
            $where = ' WHERE ' . implode(' AND ', $where);
        }
        
        $query = new K_Db_Query;
        $sql = "SELECT SQL_CALC_FOUND_ROWS type_news.*,type_section_ua_name from type_news
                LEFT JOIN tree ON tree_id = type_news_id 
                LEFT JOIN type_section ON type_section_id = tree_pid 
        $where order by type_news_date DESC LIMIT $start, $onPage";

        $itemsRes = $query->q($sql);

        $sql = "SELECT FOUND_ROWS() as countItems;";
        $countItems = $query->q($sql);
        $countItems = $countItems[0]['countItems'];

        $items = array();

        foreach ($itemsRes as $v) {
            $itemRow['id'] = $v['type_news_id'];
            $itemRow['date'] = $v['type_news_date'];
            $itemRow['title'] = strip_tags(htmlspecialchars ($v['type_news_title']));
            $itemRow['lang'] =strip_tags($v['type_news_lang']);
            $itemRow['section'] =strip_tags($v['type_section_ua_name']);
            $itemRow['author'] = strip_tags(htmlentities($v['type_news_author']));
            
            $items[] = $itemRow;
        }
   
        $returnJson = array(
            'error' => false,
            'items' => $items,
            'countItems' => $countItems);

        $this->putJSON($returnJson);
    }
    
    
    public function settagsAction(){
     
        $blogId = intval($_POST["this_key"]);
        $blogTagsModel = new Admin_Model_BlogTag; 
        
        if($blogId){
        
             if(is_array($_POST["tags"])){
                
                 $blogTagsModel->removeID($blogId);
                 
                 foreach($_POST["tags"] as $v){
                    
                    $blogTagsModel->save(array('bt_blog_id'=>$blogId,
                                              'bt_tag_id'=>$v
                                        ));
                 }
             }else{
                
                 $jsonReturn['error'] = true;
                 $jsonReturn['msg'] = array('1'=>array('label'=>'Теги','error'=>'Ошибка в тегах, должен быть массив'));
                 $this->putJSON($jsonReturn);
                 
             }
        
             $jsonReturn['error'] = false;
             $jsonReturn['msg'] ='Теги для блога установлены';
             $jsonReturn['form'] = false;    
             
         }else{
            
             $jsonReturn['error'] = true;
             $jsonReturn['msg'] = array('1'=>array('label'=>'BlogID','error'=>'Ошибка'));
             
         }
         
         $this->putJSON($jsonReturn);
    }
    
     
    public function addtagAction(){
    
        $tagname = $_POST["tagname"];
    
        if($tagname){
            
          $tagModel = new Type_Model_BlogTag;
        //addNode($typeModel, $type, $tree, $nodeTitle, $nodeName, $saveData, $errSql = false)
        
          $nodeId =  K_CupTree::addNode($tagModel,'blogtag','/blogtags/', $tagname, $tagname,array('type_blogtag_name'=>$tagname));
        
          if($nodeId){
            
             $jsonReturn['error'] = false;
             $jsonReturn['id'] = $nodeId; 
            
          }else{
            
             $jsonReturn['error'] = true;
             $jsonReturn['msg'] = array('1'=>array('label'=>'Ошибка записи элемент типа','error'=>'Сообщите администратору'));
             $this->putJSON($jsonReturn);
          }
             
        }else{
            
             $jsonReturn['error'] = true;
             $jsonReturn['msg'] = array('1'=>array('label'=>'Название тега','error'=>'Введите название нового тега'));
             
         }
         
         $this->putJSON($jsonReturn);
    }
}
