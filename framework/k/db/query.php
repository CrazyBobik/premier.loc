<?php

class K_Db_Query {

	protected $db;
	
	public function __construct(&$adapter = null)
	{
		if (empty($adapter))
		{
			$this->db = &K_Db_Adapter::$defaultAdapter;
		}
		else
		{
			$this->setAdapter( $adapter );
		}
	}
	
	public function setAdapter(&$adapter)
	{
	   
		if ($adapter)
		{
			$this->db = &$adapter;
		}
        
	}
    
    /** @function query алиас для query 
     *  @param $sql - выполнить sql запрос
     */

	public static function query($sql, $noDbRow = true)
	{
	 
      $q = new K_Db_Query;
   	  return $q->q($sql, $noDbRow);
         
	}
	
     /** @function query алиас для query 
     *  @param $sql - выполнить sql запрос
     */
	
	public static function data($sql, $noDbRow = true)
	{
	   
		return self::query($sql, $noDbRow);
        
	}	
	
    /** @function query алиас для query 
     *  @param $sql - выполнить sql запрос
     */

	public static function row($sql, $noDbRow = true)
	{
	 
      $q = new K_Db_Query;
      $r = $q->q($sql, $noDbRow);
      
      if(!$r){
        
        return false;
        
      }
            
      return $r[0];
         
	}
    
     /** @function query алиас для query 
     *  @param $sql - выполнить sql запрос
     */

	public static function one($sql, $key, $noDbRow = true)
	{	  
		  $q = new K_Db_Query;
		  $r = $q->q($sql, $noDbRow);
		 
		  if(!$r){
			
			return false;
		  
		  }
			
		  $r = $r[0][$key]? $r[0][$key] : $r[0]['id'];
		   
		  return $r? $r : false;
 	}
    
	/** @function последний добавленный id
     *  @return $sql - выполнить sql запрос
     */
	
	public static function lastId()
	{		
	    $q = new K_Db_Query;
		return $q->db->lastInsertID();
	}	
/*
	public static function e($value)
	{		
		return ();
	}	*/
    public static function e($value)
	{		
        $q = new K_Db_Query;
		return $q->db->escape($value);
	}	
	   
	public static function quote($value)
	{	
		return k_db_quote::quote($value);
	}

	public static function qv($value)
	{		
		return self::quote($value);
	}	

	public static function qk($key)
	{		
		return k_db_quote::quoteKey($key);
	}	
		
          
        
	/** @function получить инстанс query
     *  @return $sql - выполнить sql запрос
     */
	
	public static function get($noDbRow = false)
	{	   
        $q = new K_Db_Query;
        $q->db->noDbRow = $noDbRow;
		return $q->db->query($sql);
  	}	
		
		
    /** @function q алиас для query 
     *  @param $sql - выполнить sql запрос
     *  @param $noDbRow - не использовать ActiveRecord записи $noDbRow
     */
    
	public function q($sql, $noDbRow = false)
	{
	   
        $this->db->noDbRow = $noDbRow;
		return $this->db->query($sql);
        
	}
	
	public static function columnArray($sql, $noDbRow = true)
	{
	
	    $q = new K_Db_Query;	
			
		$res = $q->q($sql, $noDbRow);
		
		foreach ($res as $row) {
		
			$columnArray[] = $row['id'];
			
		}
		
		return $columnArray;
        
	}
	
	public static function columnArrayValue($sql, $value, $noDbRow = true)
	{
	   
        $q = new K_Db_Query;	
			
		$res = $q->q($sql, $noDbRow);
		
		foreach ($res as $row) {
		
			$columnArray[$row['id']] = $row[$value];
			
		}
		
		return $columnArray;
        
	}
	
	public static function columnArrayName($sql, $noDbRow = true)
	{
	   
        $q = new K_Db_Query;	
			
		$res = $q->q($sql, $noDbRow);
		
		foreach ($res as $row) {
		
			$columnArray[$row['name']] = $row['id'];
			
		}
		
		return $columnArray;
        
	}
	
	public static function columnArrayNameZone($sql, $noDbRow = true)
	{
	   
        $q = new K_Db_Query;	
			
		$res = $q->q($sql, $noDbRow);
		
		foreach ($res as $row) {
		
			$columnArray[$row['name']] = array ('id' => $row['id'], 'zone2' => $row['zone2']);
			
		}
		
		return $columnArray;
        
	}

}