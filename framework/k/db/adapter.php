<?php

/**
 * Database Mysqli Adapter
 * 		Cachable selects
 *  	4 fetch modes
 *  	CACHE WORKS ONLY WITH K_Db_Select INSTANCES AS SQL
 *  
 *  	METHODS:
 *  		CACHE:
 *  		useCache( $cache )
 *  		disableCache()
 *  		restoreCache()
 *  
 *  		DATABASE:
 *  		selectDb( $name )
 *  		setCharset( $charset )
 *  
 *  		QUERY:
 *  		query( $sql, $count = 0, &$model = null )
 *  
 *  		SELECT DATA:
 *  		fetchReal( $sql, $count = 0, &$model = null, $calculateDuration = true )
 *  		fetch( $sql, $count = 0, &$model = null )
 *  		fetchAll( $sql, $count = 0, &$model = null )
 *  		fetchRow( $sql, &$model = null )
 *  		
 *  		UPDATE/INSERT DATA:
 *  		save( $tableName, $dataRow )
 *  		saveAll( $tableName, $dataRows )
 */

define('DESCRIBE_CACHE_PREFIX', 'describe_');
define('TABLE_LAST_MODIFY_PREFIX', 'lastmod_');

class K_Db_Adapter {
	
	static public $defaultAdapter = null;
    public $noDbRow = false;
	
	// connection options
	protected $options = array(
		'host' => 'localhost',
		'port' => '3306',
		'user' => 'user',
		'password' => '',
		'database' => 'db',
		'charset' => 'utf8',
		'structureCache' => null
	);
	
	// current database name
	protected $currentDb = 'db';
	
	// mysqli adapter
	protected $mysqli = null;
	
	// used cache for select
	protected $usedCache = null;
	protected $savedUsedCache = null;
	
	protected $structureCache = null;
	
	// last query duration (microtime)
	public $lastQueryDuration = 0;
    
    public $lastSqlQuery ='';
	public $lastQueryError = false;
	
	/**
	 * Constructor
	 */
	public function __construct( $options = array() ) {
		$this->options = array_merge( $this->options, $options );
		
		$this->currentDb = $this->options['database'];		
		
		$this->_connect();
		
		$this->setCharset( $this->options['charset'] );
					
		// set cache factory for table structures
		if ( $this->options['structureCache'] instanceof K_Cache_ICache ) {
			$this->structureCache = &$this->options['structureCache'];
		}
		
		if ( empty(self::$defaultAdapter) ) {
			self::$defaultAdapter = $this;
		}
	}
	
	/**
	 * Connect to database
	 */
	public function _connect() {
		$this->mysqli = new mysqli( $this->options['host'], $this->options['user'], $this->options['password'], $this->options['database'] );
		
		if (mysqli_connect_errno()) {
		    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
	}
	
	/**
	 * Get SQL
	 */
	protected function _query( &$obj ) {
		$sqlString = '';
		if ( $obj instanceof K_Db_Select ) {
			$sqlString = $obj->sql();
		} elseif ( is_string($obj) ) {
			$sqlString = $obj;
		}
		return $sqlString;
	}
	
	/**
	 * Use cache for SELECT SQL
	 * @param K_Cache_ICache $cache	Cache Factory (not Manager)
	 */
	public function useCache( $cache ) {
		if ( $cache instanceof K_Cache_ICache ) {
			$this->usedCache = $cache;
		}
	}
	
	/**
	 * Disable cache for SELECT SQL
	 */
	public function disableCache() {
		if ( $this->usedCache ) {
			$this->savedUsedCache = $this->usedCache;
		}
		$this->usedCache = null;
	}
	
	/**
	 * Restore cache for SELECT SQL
	 */
	public function restoreCache() {
		if ( $this->savedUsedCache ) {
			$this->usedCache = $this->savedUsedCache;
		}
	}
	
	/**
	 * Query
	 * @param String|K_Db_Select 	$sql		SQL query
	 * @param Int					$count		how many records get (0 - max)
	 */
	public function query( $sql, $count = 0, &$model = null ) {
		$this->testConnection();		
		$sqlString = $this->_query( $sql );
		if (!empty($sqlString)) {		
			return $this->fetch( $sqlString, $count, $model );
		}
        
        $this->noDbRow = false;
	}
	
	/**
	 * Select Database
	 * @param String	$name	Database name
	 */
	public function selectDb( $name ) {
		$this->testConnection();
		$this->mysqli->select_db( $name );
		$this->currentDb = $name;
	}
	
	/**
	 * Set mysql charset
	 * @param String	$charset	Charset { "utf8", "cp1251"... }	
	 */
	public function setCharset( $charset ) {		
		$this->testConnection();
		$this->mysqli->set_charset( $charset );
	}
	
	/**
	 * Test mysql connection
	 */
	protected function testConnection() {
		if ( empty($this->mysqli) ) {
			throw new Exception("Mysqli connection closed");
		}
	}

        /**
         * begin transaction
         * disable auto commit
         */
        public function beginTransaction() {
            $this->testConnection();
            $this->mysqli->autocommit( false );
        }

        /**
         * end transaction
         * enable auto commit
         */
        public function endTransaction() {
            $this->testConnection();
            $this->mysqli->commit();
            $this->mysqli->autocommit( true );
        }
	
	/**
	 * Fetch without caching & describe
	 * @param K_Db_Select/String 	$sql		SQL
	 * @param Int 					$count		row count (0 - default/unlimited)
	 */
	public function fetchReal( $sql, $count = 0, &$model = null, $calculateDuration = true ) {
	   
		$this->testConnection();
		
		$initTime = K_Time::microtime_float();
		
		$return = array();
		
		$numRows = 'N/A';
		$queryDuration = 'N/A';
			
		if ($result = $this->mysqli->query( $this->_query($sql) )) {						
			$i = 0;			
			if ( is_object($result) && $result->num_rows > 0 ) {
				$numRows = $result->num_rows; // for debug
                
				while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
				    
                     if( $this->noDbRow ){
                     
                         $return[] = $row;
                     
                     }else{				   	
    	             
                         $return[] = new K_Db_Row( $row, $model );
                     
                     } 
                       			   	
			  	   	 $i++;
                     
				   	 if( $count>0 && $i>=$count ) {
				   	
                       	break;
				   	 
                     } 
                     
				}	
                
				$result->close();
                
			}
		}
		
		if ( true ) {
			$this->lastQueryDuration = K_Time::microtime_float() - $initTime;
			$queryDuration = $this->lastQueryDuration; // for debug
		}
        
//        echo $sql."\n";
		K_Debug::get()->addSql( $sql, $numRows, $queryDuration );
        
        $this->noDbRow = false;	
        
		return $return;
	}
	
	/**
	 * Describe table data, save to cache or load from cache
	 * @param String	$name	table name
	 */
	protected function _describeTable( $name ) {
		if ( $data = $this->structureCache->load( DESCRIBE_CACHE_PREFIX.$name ) ) {
			$tableStructure[ $name ] = $data;
		} else {
			$tableStructure[ $name ] = $this->fetchReal( 'DESCRIBE '.$name );
			$this->structureCache->save( DESCRIBE_CACHE_PREFIX.$name, $tableStructure[ $name ] );
		}
	}
	
	/**
	 * Get table last modification time
	 * @param String	$name	table name
	 */
	protected function _getLastDataChange( $name ) {
		if ( $data = $this->structureCache->load( TABLE_LAST_MODIFY_PREFIX.$name) ) {
			return $data['modtime'];
		}
		return null;
	}
	
	/**
	 * Set table last modification time
	 * @param String	$name	table name
	 */
	public function _setLastDataChange( $name ) {
		$name = trim(strtolower( $name ));
		$this->structureCache->load( 
			TABLE_LAST_MODIFY_PREFIX.$name, 
			array(
				'modtime' => time()
			) 
		);
		return true;
	}
		
	/**
	 * Fetch result
	 * @param K_Db_Select/String 	$sql		SQL
	 * @param Int 					$count		row count (0 - default/unlimited)
	 */
	public function fetch( $sql, $count = 0, &$model = null ) {
		$initTime = K_Time::microtime_float();
		
		$useCache = $sql instanceof K_Db_Select && $this->usedCache instanceof K_Cache_ICache;
				
		$tableStructure = array();
		
		$lastModifyTime = 0;
				
		$sqlCacheIdMod;
		
		// get cached described data about table(s)
		if ( $sql instanceof K_Db_Select && $this->structureCache ) {
			$tableNames = $sql->table();
			if ( count($tableNames) ) {
				foreach( $tableNames as $table ) {
					$name = trim( strtolower( $table['name'] ) );
					$tableStructure[ $name ] = $this->_describeTable( $name );
					$lastDataChange = $this->_getLastDataChange( $name );
					$lastModifyTime = $lastModifyTime < $lastDataChange? $lastDataChange : $lastModifyTime;
				}
			}
			$sqlCacheIdMod = $sql->cacheId( $lastModifyTime );
		}
		
		// Load results from cache
		if ( $useCache && $this->usedCache->test( $sqlCacheIdMod ) ) {
			if ( $data = $this->usedCache->load( $sqlCacheIdMod ) ) {
				$this->lastQueryDuration = K_Time::microtime_float() - $initTime;
				return $data;
			}
		}
		
		// Run sql query
		$return = $this->fetchReal( $sql, $count, $model, false );
			
		// Save results to cache
		if ( $useCache ) {
			$this->usedCache->save( $sqlCacheIdMod, $return );
		}
		
		$this->lastQueryDuration = K_Time::microtime_float() - $initTime;
		return $return;
	}
	
	public function fetchAll( $sql, $count = 0, &$model = null ) {
		return $this->fetch( $sql, $count, $model );
	}
	
	public function fetchRow( $sql, &$model = null ) {
                $result = $this->fetch( $sql, 1, $model );
                if ( count($result) ) {
                    return $result[0];
                }
		return $result;
	}
        
    public function fetchOne( $sql, &$model = null ) {
                $result = $this->fetch( $sql, 1, $model );
                if ( count($result) ) {
                    $data = $result[0]->toArray();
                    $key = key($data);
                    return $data[$key];
                }
		return $result;
	}
	
	public function _mysqli() {
		return $this->mysqli;
	}
	
	public function save( $tableName, $dataRow ) {
		$model = new K_Db_Model( $tableName, null, $this );
		$model->save( $dataRow );
		unset( $model );
	}
	
	public function saveAll( $tableName, $dataRows ) {
		$model = new K_Db_Model( $tableName, null, $this );
		$model->saveAll( $dataRows );
		unset( $model );
	}

	public function lastInsertID() {
		return $this->mysqli->insert_id;
	}
    
   	public function escape($value) {
		return $this->mysqli->real_escape_string($value);
	}
    
}

?>