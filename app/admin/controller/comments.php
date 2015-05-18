<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Controller_Comments extends Controller {
   
   
    protected function indexAction() {
   	    $this->view->title = 'Просмотр Комментариев';
		$this->view->headers =array(
                                   array('title'=>'Комментарии',
                                    )
                              );
         $this->render('comments');  
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
                $where[] ="UNIX_TIMESTAMP(comment_date) >= ".K_Db_Quote::quote($dateStart);
            } else {
                 $where[] ="(UNIX_TIMESTAMP(comment_date) BETWEEN  ".K_Db_Quote::quote($dateStart)." AND ".K_Db_Quote::quote($dateStop).")";
            }
        } else
        if ($dateStart) {
            $where[] ="UNIX_TIMESTAMP(comment_date) >= ".K_Db_Quote::quote($dateStart);
        } else
        if ($dateStop) {
            $where[] ="UNIX_TIMESTAMP(comment_date) <= ".K_Db_Quote::quote($dateStop);
        }
        
        
        if ($newId){
          $where[] = " comment_new = " . K_Db_Quote::quote($newId);  
        }  
        if($searche){
          $where[] = " (comment_name like " . K_Db_Quote::quote($searche . '%'). " OR comment_email like " . K_Db_Quote::quote($searche . '%').")";
        }
        if($_POST['comments-status']){
            $where[] = " comment_status = " . K_Db_Quote::quote($_POST['comments-status']);
        }
        if ($where && count($where)) {
            $where = ' WHERE ' . implode(' AND ', $where);
        }
        
        $query = new K_Db_Query;
        $sql = "SELECT SQL_CALC_FOUND_ROWS * from comments $where order by comment_date DESC LIMIT $start, $onPage";

        $itemsRes = $query->q($sql);

        $sql = "SELECT FOUND_ROWS() as countItems;";
        $countItems = $query->q($sql);
        $countItems = $countItems[0]['countItems'];

        $items = array();

        foreach ($itemsRes as $v){
		
				$itemRow['id'] = $v['comment_id'];
				$itemRow['date'] = $v['comment_date'];
				$itemRow['name'] = strip_tags(htmlspecialchars ($v['comment_name']));
				$itemRow['content'] = strip_tags(htmlspecialchars($v['comment_content']));
				$itemRow['status'] =$v['comment_status'];
				$itemRow['ip'] =long2ip($v['comment_ip']);
				$items[] = $itemRow;
				
        }

        $returnJson = array(
							'error' => false,
							'items' => $items,
							'countItems' => $countItems
						);

        $this->putJSON($returnJson);
    }
    
    
     public function editAction() {
        $this->disableLayout=true;
        $commentId =intval($_GET['commentid']);  
 
        $commentModel = new Admin_Model_Comment;
       
        $commenRow = $commentModel->select('*, UNIX_TIMESTAMP(comment_date) comment_date')->where('comment_id="'.$commentId.'"')->fetchAll();

		
        $this->view->commentRow = K_CupItems::stripFields($commenRow,'comment');
		
		$this->view->commentRow = $this->view->commentRow[0];
       
        $this->render('edit');        
     } 
     
    
    public function saveAction(){
        
        $commentId = intval($this->getParam('id'));  
   
        $nameAccos = array(
                    'comment_name' => 'Имя пользователя',
                    'comment_content' => 'Комментарий',
                  );
        
        $data = array(  'comment_name' => trim($_POST['name']),
                        'comment_content' => trim($_POST['content']),
                        );
       
        $validate = array('comment_name' => array(
                                        'required' => true,
                                        'notEmpty',
                                        'maxlen' => 255,
                                        ),
         
                          'comment_content' => array(
                                        'required' => true,
                                        'notEmpty',
                                        ),
                          );
           
        $commentModel = new Admin_Model_Comment;
        
        if ($commentModel->isValidRow($data, $validate)){
         
            $commentModel->update($data,'comment_id='.$commentId);
            $jsonReturn['error'] = false;
            $jsonReturn['msg'] = 'Данные сохранены';
       
        } else {
       
            $jsonReturn['error'] = true;
            $jsonReturn['msg'] = $commentModel->getErrorsD($nameAccos);
      
        }
        $this->putJSON($jsonReturn);
  }

  public function removeAction() {
        $commentModel = new Admin_Model_Comment;
        $commentId = intval($_POST['commentid']);
     
        if($commentId){
            $commentModel->removeID($commentId);
            $this->putJSON(array('error' => false));
        }else{
            $this->putJSON(array('error' => true, 'msg' =>'Неверный индитификатор'));
        }
   }

   public function changestatusAction() {
        $commentModel = new Admin_Model_Comment;
        $commentId = intval($this->getParam('id'));
        if (in_array($_POST['param'], array(
            'опубликован',
            'ожидает публикации'
            ))) {
            $status = $_POST['param'];
        }

        if ($status && $commentId) {
            $commentModel->update(array('comment_status' => $status), array('comment_id' => $commentId));
            $this->putAjax("OK");
        } else {
            $this->putAjax("ERROR");
        }
   }
}
