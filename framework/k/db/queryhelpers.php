<?php

class K_Db_QueryHelpers {
	
	public static function where($whereArray)
	{	   
		$select = new k_db_select();
		
		return $select->_where($whereArray);
	 
  	}	
 
}