<?php 

/**
 * Class Model
 * 
 * Validation example
 * <example>
 * 	if ( $model->isValid( $data ) ) {
 *		echo 'valid';
 *	}
 * </example>
 * 
 * Find, validation & save results example
 * <example>
 *  $model = new K_Db_Model( 'users' );
 *  $data = $model->find( K_Db_Select::create()->where( 'name LIKE "A%"' ) );
 *  $data[0]['name'] = "Alex";
 *  if ( $model->isValid( $data ) ) {
 *  		$model->saveAll( $data );
 *  }
 * </example>
 * 
 * Validation of 1 row
 * <example>
 *  $model = new K_Db_Model( 'users' );
 *  $data = $model->find( K_Db_Select::create()->where( 'name LIKE "A%"' ) );
 *  $data[0]['name'] = "Alex";
 *  $validators = array(
 *  	'name' => array(
 *  		'/.{5,}/' // min length 5
 *  	)
 *  ) 
 * 
 * 
 *  if ( $model->isValidRow( $data[0], $validators ) ) { // or without $validators for use model's validators
 *  	$data[0]->save(); // or $model->save( $data[0] );
 *  }
 * 
 * 
 * 
 * 
 * 
 * </example>
 */

define ('K_LINKTYPE_ONE_ONE',      'one-to-one');      // LINK
define ('K_LINKTYPE_ONE_MANY',     'one-to-many');     // SELECT BOX
define ('K_LINKTYPE_MANY_ONE',     'many-to-one');     // OPTIONS
define ('K_LINKTYPE_MANY_MANY',    'many-to-many');    // MULTISELECT BOX

define ('K_FOREIGN_CASCADE', 'cascade');
define ('K_FOREIGN_RESTRICT', 'restrict');

class K_Db_Model extends K_Validator {
    
	var $name;
	var $primary;
        var $foreign; // use in K_Db_Select
        /*var $foreign = [
            'usersInfo' => [
                'user_id' => [
					'key' 		=> '',
                    'type'      => K_LINKTYPE_ONE_ONE,
                    'delete'    => 'cascade',
                    'update'    => 'none'
                ]
            ]
        ];*/
        
	protected $db;

	public $data = null;        
	
	public function __construct( $name = null, $primary = null, &$adapter = null ) {
                parent::__construct();

		if ( !empty($name) ) {
			$this->name = $name;
		}
		
		if ( !empty($primary) ) {
			$this->primary = $primary;
		}
		
		if ( empty($adapter) ) {
			$this->db = &K_Db_Adapter::$defaultAdapter;
		} else {
			$this->setAdapter( $adapter );
		}
	}
	
	public function setAdapter( &$adapter ) {
		if ( $adapter ) {
			$this->db = &$adapter;
		}
	}
        
    public function setForeignKeys( $keys ) {
            if ( is_array($keys) ) {
                $this->foreign = $keys;
            }
    }
    
    /** function find âîçâðàùÿåò àññîöèàòèâíûé ìàññèâ ôîðìàòà array['$keyField'] = $valueField
      *  @param $sql - óñëîâèå âûáîðêè 
      *  @param $count - êîëè÷åñòâî
      *  @return íàáîð ñòðîê K_DB_Row
      */   
	
	public function find( $sql = null, $count = 0 ) {
	   
		if ( $sql instanceof K_Db_Select ) {
			$sql->from( $this->name );	
			
		}elseif (is_int($sql)) {
		
			return $this->findId($sql);
			
		}
		elseif ( empty($sql) ) {
			$sql = K_Db_Select::create()->from( $this->name );
		}
		return $this->db->fetch( $sql, $count, $this );
        
	}

	public function findId($id) {
   	    
	    $sql = "SELECT * FROM ".$this->name.' WHERE '.' '.$this->primary.'='.intval($id);
	
		return $this->db->fetch( $sql, 1, $this );
        
	}    

     /** function fetchAll àëèàñ ê find âîçâðàùÿåò ìàññèâ çàïèñåé
      *  @param $sql - óñëîâèå âûáîðêè 
      *  @param $count - êîëè÷åñòâî
      *  @return íàáîð ñòðîê K_DB_Row
      */

    public function fetchAll( $sql = null, $count = 0 ){
        
            return $this->find( $sql, $count );
            
    }
        
     /** function fetchMap âîçâðàùÿåò àññîöèàòèâíûé ìàññèâ ôîðìàòà array['$keyField'] = $valueField
      *  @param $keyField - ïîëå êëþ÷à 
      *  @param $valueField - ïîëå çíà÷åíèÿ
      *  @param $sql - óñëîâèå âûáîðêè 
      *  @param $count - êîëè÷åñòâî
      *  @param $keyPrintFormat - ôîðìàò çàïèñè êëþ÷à
      *  @return array['$keyField'] = $valueField    
      */
        
    public function fetchMap( $keyField, $valueField, $sql = null, $count = 0, $keyPrintFormat = '%s' ){
        
            $result = $this->find( $sql, $count );
            $map = array();
            if ( count($result) ) {
                if ( $keyPrintFormat !== '%s' ) {
                    foreach ($result as $row) {
                        $map[ sprintf( $keyPrintFormat, $row[$keyField] ) ] = $row[$valueField];
                    }
                } else {
                    foreach ($result as $row) {
                        $map[ $row[$keyField] ] = $row[$valueField];
                    }
                }
            }
            unset($result);
            return $map;
            
        }
        
     /** function fetchAssoc âîçâðàùÿåò àññîöèàòèâíûé ìàññèâ ôîðìàòà array['$keyField'] = $valueField
      *  @param $keyField - ïîëå êëþ÷à ïî êîòîðîìó ôîðìèðîâàòü ìàññèâ ñòðîê
      *  @param $sql - óñëîâèå âûáîðêè 
      *  @param $count - êîëè÷åñòâî ñòðîê
      *  @return array['$keyField'] = $valueField    
      */ 
        
    public function fetchAssoc( $keyField, $sql = null, $count = 0 ) {
            $result = $this->find( $sql, $count );
            $map = array();
            if ( count($result) ) {
                foreach ($result as $row) {
                    $map[ $row[$keyField] ] = $row;
                }
            }
            unset($result);
            return $map;
        }
        
     /** function fetchArray âîçâðàùÿåò ìàññèâ 
      *  @param $sql - óñëîâèå âûáîðêè 
      *  @param $count - êîëè÷åñòâî ñòðîê
      *  @return array
      */   
 
    public function fetchArray( $sql = null, $count = 0 ) {
            $result = $this->find( $sql, $count );
            $map = array();
            if ( count($result) ) {
                foreach ($result as $row) {
                    $map[] = $row->toArray();
                }
            }
            unset($result);
            return $map;
        }
        
     /** function fetchRow âîçâðàùÿåò ñòðîêó - àññîöèàòèâíûé ìàññèâ 
      *  @param $sql - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @return array
      */   
        
    public function fetchRow( $sql = null ){
            if ( $sql instanceof K_Db_Select ) {
                    $sql->from( $this->name );
            } elseif ( empty($sql) ) {
                    $sql = K_Db_Select::create()->from( $this->name );
            }
            return $this->db->fetchRow( $sql, $this );
        }
        
     /** function fetchOne âîçâðàùÿåò îäèí ýëåìåíò  
      *  @param $sql - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @return array
      */    
       
    public function fetchOne( $sql = null ){
        
            if ( $sql instanceof K_Db_Select ) {
                    $sql->from( $this->name );
            } elseif ( empty($sql) ) {
                    $sql = K_Db_Select::create()->from( $this->name );
            }
            
            $sql->limit(1); // @TODO Nasledovanie svoistva
            
            return $this->db->fetchOne( $sql, $this );
        }
    
     /** function getResultMap âîçâðàùÿåò ðåçóëüòàòèâíûé ìàññèâ  
      *  @param $data - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @param $key - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @param $value - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @param $keyPrintFormat - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @return array
      */     
        
    public function getResultMap( &$data, $key, $value, $keyPrintFormat = '%s' ) {
            if (!count($data)) {
                return array();
            }
            $result = array();
            foreach($data as $item) {
                $result[ sprintf( $keyPrintFormat, $item[$key] ) ] = $item[$value];
            }
            return $result;
        }
        
     /** function getResultKeys âîçâðàùÿåò ðåçóëüòèðóþùèå êëþ÷è 
      *  @param $data - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @param $key - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @param $value - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @param $keyPrintFormat - óñëîâèå âûáîðêè ôîðìèðóåòñÿ ñ ïîìîùüþ K_Db_Select
      *  @return array
      */       
        
    public function getResultKeys( &$data, $key, $printFormat = '%s' ) {
            if (!count($data)) {
                return array();
            }
            $result = array();
            foreach($data as $item) {
                if ( !in_array( $item[$key], $result ) ) {
                    $result[] = sprintf( $printFormat, $item[$key] );
                }
            }
            return $result;
        }
        
    public function select( $fields = array() ) {
            return K_Db_Select::create( $fields )->from($this);                    
        }

	public function count( $sql = null ) {
		if ( $sql instanceof K_Db_Select ) {
                        $sqlTable = $sql->table();
                        if ( empty($sqlTable) ) {
                            $sql->from( $this->name );
                        }			
			$sql->fields( array( 'rcount' => new K_Db_Expr('COUNT(0)') ) );
		} else {			
			throw new Exception('K_Db_Model->count :: can be work only with K_Db_Select objects');
			return null;
		}
		$result = $this->db->fetch( $sql, 0, $this );
		return isset($result[0], $result[0]['rcount'])?$result[0]['rcount']:0;
	}
	
	protected function _implode( $symbol, $data ) {
		if ( is_array($data) ) {
			$update = array();
			foreach ($data as $key => &$item) {
				//$item = K_Db_Quote::quote( $item );
                  // ïðîâåðêà íà íóë, òåïåðü ìîæíî óñòàíàâëèâàòü çíà÷åíèå null â áàçó
                 $item=is_null($item)? 'null' : K_Db_Quote::quote( $item );
        		 $update[] = $key.'='.$item;
			}
			return array( 'values' => implode($symbol, $data), 'update' => implode($symbol, $update) );
		}
		return array( 'values' => '', 'update' => '' );
	}
	
	public function isValidRow( &$row, &$validate = null ) {
		$jData;
		if ( $row instanceof K_Db_Row ) {
			$jData = $row->toArray();	
		} else {
			if ( !empty($row) && isset($row[ $this->name ]) && is_array($row[ $this->name ]) ) {
				$jData = &$row[ $this->name ];
			} else {
				$jData = &$row;
			}
		}
		
		if ( is_array($jData)) {
			return $this->valid( $jData, $validate );
		}
		return false;
	}
        
    public function isValidPost() {
            return $this->isValidRow( $_POST );            
    }
	
	public function isValid( &$data = null, &$validate = null ) {
		if ( is_array($data) && count($data) ) {
			$i = 0;			
			foreach( $data as &$item ) {
				if ( !$this->isValidRow( $item, $validate ) ) return false;				
			}
			return true;
		}
		return false;
	}
    
    
    /**
     * @function update - îáíàâëåíèå çàïèñè â òàáëèöå 
     * 
     * @param $data - ìàññèâ äàííûõ
     * 
     * @param $where - óñëîâèÿ ïî êîòîðûì îáíîâëÿòü çàïèñè 
     * 
     */ 
       
        
    public function update( $data, $where = null, $setModTime = true ) {
        
            $set = array();
            if ( count($data) ) {
                foreach( $data as $key => $value ) {
                    
                    if($key != $this->primary){
                    
                      $v=is_null($value)? 'null' : K_Db_Quote::quote( $value );
                      $set[] = K_Db_Quote::quoteKey( $key ).' = '.$v;
                      
                    }
                    
                }
            }
            
            $whereString = '1=1';
            
            if ( !empty($where) ) {
                if ( $where instanceof K_Db_Select ) {
                    $whereString = $where->where;
                } elseif ( is_array($where) ) {
                    $select = K_Db_Select::create();
                    $whereString = $select->_where( array( $where ) );
                } elseif ( is_string($where) ) {
                    $whereString = $where;
                }
            }
            
            $sql = 'UPDATE '.$this->name.' SET '.implode(',', $set). ' WHERE '.$whereString;
            
            $this->db->query( $sql );
            
            if ( $setModTime ) {
                $this->db->_setLastDataChange( $this->name );
            }
    }
	
    /**
     * @function save - ñîõðàíåíèå íîáîðà äàííûõ 
     * 
    */ 
   
	public function save( $data = null, $setModTime = true ) {
		$jData;
		
		if ( $data instanceof K_Db_Row ) {
			$jData = $data->toArray();	
		} else {
			if ( !empty($data) && isset($data[ $this->name ]) && is_array($data[ $this->name ]) ) {
				$jData = &$data[ $this->name ];
			} else {
				$jData = &$data;
			}
		}
			
		if ( is_array($jData) ) {
			$imp = $this->_implode(',', $jData);
            $sql = 	'INSERT INTO '.$this->name.' ('.implode( ',',array_keys( $jData ) ).') '.
					'VALUES ('.$imp['values'].') '.
					'ON DUPLICATE KEY UPDATE '.$imp['update'];
			
			$this->db->query( $sql );
			
			if ( $setModTime ) {
                            $this->db->_setLastDataChange( $this->name );
			}
                        
                        return $this->lastInsertID();
		}
                return FALSE;
	}

    /**
     * @function lastInsertID - ïîëó÷èòü id ïîñëåäíåé äîáàâëåííîé çàïèñè 
     * 
    */ 

	public function lastInsertID() {
		return $this->db->lastInsertID();
	}
	
	    /**
     * @function lastInsertID - ïîëó÷èòü id ïîñëåäíåé äîáàâëåííîé çàïèñè 
     * 
    */ 

	public function lasID() {
		return $this->db->lastInsertID();
	}
	
	
    /**
     * @function saveAll - ñîõðàíåíèå íàáîðà äàííûõ â ñòðîêó 
     * 
    */ 
     
	public function saveAll( &$data ) {
	   
		if ( is_array($data) && count($data) ) {
			$this->db->_mysqli()->autocommit(FALSE);
			foreach( $data as &$item ) {
				$this->save( $item, false );
			}
			$this->db->_setLastDataChange( $this->name );
			$this->db->_mysqli()->commit();
			$this->db->_mysqli()->autocommit(TRUE);
		}
        
	}
    
    
    /**
     * @function remove - óäàëåíèå çàïèñåé ïî óñëîâèþ where
     * @param $sql - óñëîâèå íà óäàëåíèå çàïèñåé
    */
  
	public function remove( $sql ) {
	   
		if ( $sql instanceof K_Db_Select ) {
			$sql->from( $this->name );
		} elseif ( empty($sql) ) {
			$sql = K_Db_Select::create()->from( $this->name );
		}
		$this->db->query( 'DELETE FROM '.$this->name.' WHERE '.$sql->getCompiledWhere() );
	}
     
    public function removeID( $id ) {
        
        $removeId = intval( $id );
        
         if (!$removeId){
            return false;
         }
         
         $this->db->query('DELETE FROM ' . $this->name . ' WHERE ' . $this->primary .'="'. $removeId .'"');
         
    }
    
    /**
     * @function beginTransaction - íà÷àëî òðàíçàêöèè
     * 
     */
  
    public function beginTransaction() {
            $this->db->beginTransaction();
    }
    
    /**
     * @function endTransaction - îêîí÷àíèå òðàíçàêöèè
     * 
     */

    public function endTransaction() {
            $this->db->endTransaction();
    }
    
    /********************** ìåòîäû-àëèàñû äëÿ óïðîùåííîé çàïèñè ***************************/
    
    /**
     * @function removeID - delete id
     * @param $id - èíäåòèôèêàòîð óäàëÿåìîé çàïèñè 
     * 
     * model->mfa(select()->where())   
     *  
     * model->mfr(select()->where())
     *  
     * model->mfs($keyField, select()->where()) 
     *  
     * model->one(select()->where()) 
     *  
     */
    
    /** @function mfa - 
     * 
     */
    
    public function mfa($sql = null, $count = 0) {
        
        return  $this->fetchAll($sql, $count);
            
    }
    
    /** @function mfr -
     * 
     */
       
    public function hash($sql = null, $count = 0) {
        
       return  $this->fetchArray($sql, $count);
            
    }
    
    /** @function row - Возвращяет одну строку и базы, короткий алиас к fetchRow
     * 
     */
       
    
    public function row($sql = null) {
    
       return  $this->fetchRow($sql);
            
    }
    
    
    /** @function mfs - àëèàñ ê fetchAssoc âûáîð àññîöèàòèâíîãî ìàññèâà
     *  @param $keyField - ïîëå ïî êîòîðîìó ñòðîèòü ìàññèâ
     * 
     * 
     */
    
    public function mfs($keyField, $sql = null, $count = 0) {
        
        return  $this->fetchAssoc($keyField, $sql, $count);
            
    }
    
    /** @function mfo - àëèàñ ê fetchOne âûáîð àññîöèàòèâíîãî ìàññèâà
     *  
     */
    
    public function one($sql = null) {
        
        return  $this->fetchOne($sql);
            
    }
         
	 /** function mfm àëèàñ ê fetchMap âîçâðàùÿåò àññîöèàòèâíûé ìàññèâ ôîðìàòà array['$keyField'] = $valueField
	  *  @param $keyField - ïîëå êëþ÷à 
	  *  @param $valueField - ïîëå çíà÷åíèÿ
	  *  @param $sql - óñëîâèå âûáîðêè 
	  *  @param $count - êîëè÷åñòâî
	  *  @param $keyPrintFormat - ôîðìàò çàïèñè êëþ÷à
	  *  @return array['$keyField'] = $valueField    
	  */
    
    public function mfm($keyField, $valueField, $sql = null, $count = 0, $keyPrintFormat = '%s') {
        
        return  $this->fetchMap($keyField, $valueField, $sql = null, $count = 0, $keyPrintFormat = '%s');
            
    }
    
}

?>