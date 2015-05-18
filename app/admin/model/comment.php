<?php

defined('K_PATH') or die('DIRECT ACCESS IS NOT ALLOWED');

class Admin_Model_Comment extends Model {
    var $name = 'comments';
    var $primary = 'comment_id';
    

 protected function blogNotExists(&$text, $fieldName){
    
      $blogModel = new Type_Model_Blog;
      $result = $blogModel->fetchRow(K_Db_Select::create()->where(array('type_blog_id' => $text)));
        if ($result && count($result)) {
          return true;
        } 
        $this->errors[$fieldName] = 'NEWS_ID_ERROR';
          return false;
 }

}

?>