<?php 

/**
 * Class K_Db_Select
 * <example>
 * $select = new K_Db_Select();
 * $select
    ->fields( 
        array(
            '*', 
            'ExtraValue'=>K_Db_Select::create()
                                        ->fields('count(*)')
                                        ->from('machine')
                                        ->limit(1) 
        ) 
    )
    ->from( 'table' )
	->join( null, array( 'aliasTable2' => 'table2' ), array( 'a >=' => '2', 'b > a' ), 'right' ),
	->join( null, $model2 )
	->join( $model2, $model3 )
    ->_join( array( 'aliasTable2' => 'table2' ), array( 'a >=' => '2', 'b > a' ), 'right' )
    ->_join( 'table3', 'a > b' )
    ->where(
        array(
            'OR' => array(
                'name like' => "%abrikos%",
                'AND'=> array(
                    'b<=' => 'c',
                    'd' => 'e'
                )
            ),
            'NOT' => array('x' => 'y')
        )
    )
    ->order( array('id ASC', 'summ DESC') )
    ->limit( 10 )
    ->offset( 50 )
    ->group( 'id' );
 * </example>
 */

class K_Db_Select {
	
        /* Properties */
    protected $originalModel = null;// first from model
    protected $table = array();	// table name(s)
	protected $fields = '*'; 	// fields
	protected $where = '1=1'; 	// where string
    protected $having = null;   // having
	protected $order = ''; 		// order by
	protected $group = ''; 		// group by
	protected $limit = 0; 		// limit
	protected $offset = 0; 		// offset
	protected $cacheID = '';	// cacheID
	protected $joins = array();     // joins
    protected $joinTables = array(); // builded string
    protected $multiJoinArray = array();     // use with foreign keys in models
    protected $motherModel = null;
        
    /**
	 * Construct
	 */
	public function __construct( $fields = null ) {
            if ( !empty($fields) ) {	
                $this->fields( $fields );
            }
	}
	
	static public function create( $fields = null ) {
            return new K_Db_Select( $fields );
	}
	
	public function table() {
            return $this->table;
	}
        
        /**
         * Set mother model
         * @param K_Db_Model $model
         * @return \K_Db_Select
         */
    public function mother( $model ) {
           if ( $model instanceof K_Db_Model ) {
                $this->motherModel = $model;
            }
            return $this;
    }
        
        /**
         * Get mother model
         * @return K_Db_Model/null
         */
    public function getMother() {
            return $this->motherModel;
    }
	        
        /**
         * Remove in mother
         * @return boolean
         */        
    public function remove() {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->remove: mother model is empty');
                return false;
            }
            return $this->motherModel->remove( $this );
    }
        
    public function count() {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->count: mother model is empty');
                return false;
            }
            return $this->motherModel->count( $this );
    }
        
        public function fetchRow() {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->fetchRow: mother model is empty');
                return false;
            }
            return $this->motherModel->fetchRow( $this );
        }
        
        public function fetchOne( $fieldName = null ) {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->fetchRow: mother model is empty');
                return false;
            }
            if ( !empty($fieldName) ) {
                $this->fields( $fieldName );
            }
            return $this->motherModel->fetchOne( $this );
        }
        
        public function find( $count = 0 ) {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->find/fetchAll: mother model is empty');
                return false;
            }
            return $this->motherModel->find( $this, $count );
        }
        
        public function fetchAll( $count = 0 ) {
            return $this->find( $count );
        }
        
        public function fetchMap( $keyField, $valueField, $count = 0, $keyPrintFormat = '%s' ) {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->fetchMap: mother model is empty');
                return false;
            }
            $this->fields( array( $keyField, $valueField ) );
            return $this->motherModel->fetchMap( $keyField, $valueField, $this, $count, $keyPrintFormat );
        }
        
        public function fetchArray( $count = 0 ) {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->fetchArray: mother model is empty');
                return false;
            }
            return $this->motherModel->fetchArray( $this, $count );
        }
        
        public function fetchAssoc( $keyField, $count = 0 ) {
            if ( !$this->motherModel ) {
                throw new Error('K_Db_Select->fetchAssoc: mother model is empty');
                return false;
            }
            return $this->motherModel->fetchAssoc( $keyField, $this, $count );
        }
        
        public function update( $data ) {
            if ( !$this->motherModel ) return false;
            return $this->motherModel->update( $data, $this );
        }
        
	/**
	 * Set from table
	 */
	public function from( $table, $alias = null ) {
            unset( $this->table );
            $this->table = array();
            if ( is_string($table) && strlen(trim($table)) ) {
                
                $this->table[] = array( 'sql' => K_Db_Quote::quoteKey($table), 'name'=>$table, 'type'=>'table' );
                
            } elseif ( is_array($table) && count($table) ) {
                
                foreach( $table as $tableAlias => $tableName ) {
                    $asString = '';
                    if ( !is_numeric($tableAlias) ) {
                        $asString = 'as '.K_Db_Quote::quoteKey($tableAlias);
                    }
                    $this->table[] = array( 'sql' => K_Db_Quote::quoteKey($tableName).$asString, 'type'=>'table', 'name'=>$tableName );
                }
                
            } elseif ( $table instanceof K_Db_Model ) {
                // Set as mother model
                $this->originalModel = $table;
                if ( empty($this->motherModel) ) {
                    $this->mother( $table );
                }
                
                $this->table[] = array( 'sql' => K_Db_Quote::quoteKey($table->name), 'type' => 'table', 'name' => $table->name );                        
                
            } elseif ( $table instanceof K_Db_Select ) {
                
                if ( empty($alias) ) {
                    $alias = md5( K_Time::microtime_float() );
                }
                
                $this->table[] = array( 'sql' => $table->sql(), 'type' => 'select', 'name' => $alias );                        
                
            } else {
                throw new Exception(__CLASS__.'->'.__METHOD__.': table name is not a string');
            }

            return $this;
	}
	
	public function cache( $cacheID ) {
            $this->cacheID = $cacheID;
	}
	
	public function generateCache( $salt = '' ) {
            $this->cacheID = md5( $this->sql() ).'_'.$salt;
	}
	
	public function cacheId( $salt = '' ) {
            if (!$this->cacheID) {
                $this->generateCache( $salt );
            }
            return $this->cacheID;
	}
	
        /**
         * Function for multiple join models
         * Warning! Use odd & not odd params
         * @param K_Db_Model               $model1
         * @param K_Db_Model/string        $table2
         * @return this
         * <example>
         *      $sql->multijoin( $model1, $model2, 'inner' );
         * </example>
         */
        public function join( $model1, $table2, $type = 'left', $condition = null  ) {
 			
            if ( !empty($model1) && !($model1 instanceof K_Db_Model) ) {
                throw new Exception(__CLASS__.'->'.__METHOD__.': model1 not an instance of K_Db_Model');
                return $this;
            }
            
            $table2name = '';
            if ( $table2 instanceof K_Db_Model ) {
                $table2name = $table2->name;
            } elseif ( is_string($table2) ) {
                $table2name = $table2;
            } else {
                $table2name = null;
            }
            
            $this->multiJoinArray[] = array(
                'model1' => $model1,
                'table2name' => $table2name,
                'type' => $type,
                'condition' => $condition
            );

                        
            return $this;
        }
        
        
        /**
	 * @param Array(alias => tableName)/String(tableName) 	$table		for string table name, for array table name with alias
	 * @param Array/String									$condition	where "on"
	 * @param String 										$type		join type left, right, inner
	 */
	public function _join( $table = array(), $condition = '', $type = 'left'  ) {
            $this->joins[] = array(
                'table' => $table,
                'condition' => $condition,
                'type' => $type
            );

            return $this;
	}
	
    
     /**
     * External use in castom query
     * 
     *    
    */
    
     public function fieldsExt( $fields = null ) {
	        $retFields ='';
       
            if ( is_string($fields) ) {
                $retFields = $fields;
            } elseif ( is_array($fields) ) {
                $retFields = '';
                $i = 0;
                foreach( $fields as $key => &$value ) {
                    $val = '0';

                    if ( $value instanceof K_Db_Select ) {
                            $val = '('.$value->sql().')';
                    } elseif ( $value instanceof K_Db_Expr ) {
                            $val = $value->__toString();
                    } elseif ( is_string($value) ) {
                            $val = $value;
                    } elseif ( is_double($value) ) {
                            $val = str_replace(',', '.', (string)$value);
                    }

                    if ( is_string($key) ) {
                            $retFields .= $val.' as '.K_Db_Quote::quote($key);
                    } elseif ( $val == '*' ) {
                        $retFields .= '*';
                    } else {
                        $retFields .= K_Db_Quote::quoteKey($val);
                    }

                    if ( $i < count($fields)-1 ) {
                            $retFields .= ',';
                    }
                    $i++;
                }
            } elseif ( empty($fields) ) {
                $retFields = '*';
            } elseif ( $fields instanceof K_Db_Expr ) {
                $retFields = $fields->__toString();
            }

            return $retFields;
	}
	/**
	 * Set fields
	 */
    public function fields( $fields = null ) {
        $this->fields=$this->fieldsExt($fields);
         return $this;
    }    
	
	/**
	 * Improved implode =)
	 */
	protected function _implode( $symbol, $data ) {
            if ( is_array($data) ) {
                foreach ($data as $key => &$item) {
                    $arr = array( $key => $item );
                    $data[ $key ] = $this->_where( $arr );
                }
                return implode($symbol, $data);
            }
            return '';
	}
	
	/**
	 * Assemble where string
	 */
	public function _where( $data ) {
		$sql = '';
		if ( is_array($data) ) {
			$i=0;
			foreach ($data as $key => $value) {
				$iterSql = '';
				if ( is_string($key) ) {
					$key = trim($key);

                                        if (strlen($key)>=8 && strlen($iterSql)==0) {
						$last8symbols = mb_strtoupper(mb_substr($key, mb_strlen($key)-8, 8) );
						$ckey = K_Db_Quote::quoteKey( trim(mb_substr($key, 0, mb_strlen($key)-8)) );
						switch ( $last8symbols ) {
							case 'NOT LIKE':
								if ( $value instanceof K_Db_Select ) {
									$iterSql = $ckey.'NOT LIKE ('.$value->sql().')';
								} elseif ( is_array($value) ) {
									$iterSql = $ckey.'NOT LIKE ('.$this->_implode(',', $value ).')';
								} else {
                                                                    	$iterSql = $ckey.'NOT LIKE '. K_Db_Quote::quote($value);
                                                                }
								break;
						}
					}
                                        
                                        if (strlen($key)>=6 && strlen($iterSql)==0) {
						$last6symbols = mb_strtoupper(mb_substr($key, mb_strlen($key)-6, 6) );
						$ckey = K_Db_Quote::quoteKey( trim(mb_substr($key, 0, mb_strlen($key)-6)) );
						switch ( $last6symbols ) {
							case 'NOT IN':
								if ( $value instanceof K_Db_Select ) {
									$iterSql = $ckey.'NOT IN ('.$value->sql().')';
								} else {
									$iterSql = $ckey.'NOT IN ('.$this->_implode(',', $value ).')';
								}
								break;
						}
					}
                                        
					if (mb_strlen($key)>=5 && mb_strlen($iterSql)==0) {
						$last5symbols = mb_strtoupper(mb_substr($key, mb_strlen($key)-5, 5) );
						$ckey = K_Db_Quote::quoteKey(mb_substr($key, 0, mb_strlen($key)-5));
						switch ( $last5symbols ) {
							case ' LIKE':
								$iterSql = $ckey.' LIKE '.$this->_where( $value );
								break;
						}
					}

					if (strlen($key)>=3 && strlen($iterSql)==0) {
						$last3symbols = mb_strtoupper(mb_substr($key, mb_strlen($key)-3, 3) );
						$ckey = K_Db_Quote::quoteKey( trim(mb_substr($key, 0, mb_strlen($key)-2)) );
						switch ( $last3symbols ) {
							case ' IN':
								if ( $value instanceof K_Db_Select ) {
									$iterSql = $ckey.' IN ('.$value->sql().')';
								} else {
									$iterSql = $ckey.' IN ('.$this->_implode(',', $value ).')';
								}
								break;
						}
					}

					if (strlen($key)>=2 && strlen($iterSql)==0) {
						$last2symbols = mb_strtoupper(mb_substr($key, mb_strlen($key)-2, 2) );
						$ckey = K_Db_Quote::quoteKey(trim(mb_substr($key, 0, mb_strlen($key)-2)));
						switch ( $last2symbols ) {
							case '<=':
							case '>=':
							case '!=':
								$iterSql = $ckey.$last2symbols.$this->_where( $value );
								break;
						}
					}

					if ( strlen($key)>=1 && strlen($iterSql)==0 ) {
                                                $last1symbol = mb_strtoupper(mb_substr($key, mb_strlen($key)-1, 1) );
                                                $ckey = K_Db_Quote::quoteKey(trim(mb_substr($key, 0, mb_strlen($key)-1)));
						switch ( $last1symbol ) {
							case '<':
							case '>':
								$iterSql = $ckey.$last1symbol.$this->_where( $value );
								break;
							case '=':
								if ( is_array($value) ) {
									$iterSql = $ckey.' IN ('.$this->_implode(',', $value).')';
								} else {
									$iterSql = $ckey.' = '.$this->_where( $value );
								}
								break;
							default:
								if ( is_array($value) ) {
									$iterSql = K_Db_Quote::quoteKey($key).' IN ('.$this->_implode(',', $value).')';
								} else {
									$iterSql = K_Db_Quote::quoteKey($key).' = '.$this->_where( $value );
								}
								break;
						}
					}

					switch ( strtoupper($key) ) {
						case 'OR':
						case 'AND':
						case 'XOR':
								$iterSql = '('.$this->_implode(') '.strtoupper($key).' (', $value).')';
							break;
						case 'NOT':
								$iterSql = 'NOT ('.$this->_where( $value ).')';
							break;
					}

				} else {
					if ( is_array($value) ) {
						$iterSql = '('.$this->_implode(') AND (', $value).')';
					} else {
						$iterSql = $this->_where( $value );
					}
				}

				$i++;
				$sql .= $iterSql;
			}
                } elseif ( $data instanceof K_Db_Select ) {
                        $sql .= '('.$data->sql().')';
		} elseif ( is_string($data) || is_numeric($data) ) {
			$sql .= K_Db_Quote::quote($data);
		} elseif ( $data instanceof K_Db_Expr ) {
                        $sql .= $data->__toString();
                }
		return $sql;
	}
	
    public function whereExt($data) {
		if ( is_string($data) ) {
			return  $data;
		} elseif ( is_array($data) && count($data) ) {
			return $this->_where( array( $data ) );
                } else {
			return '1=1';
		}
 	}  
    
    
	/**
	 * Set where
	 */
	public function where($data) {
        {
             $this->where=$this->whereExt($data);
      		 return $this;
        }
	}
    
    
	
	/**
	 * Set order by
	 */
	public function order( $data ) {
		if ( is_string($data) ) {
			$this->order = $data;
		} elseif ( is_array($data) ) {
			$this->order = implode(',', $data);
		} else {
			$this->order = '';
		}
		return $this;
	}
	
	/**
	 * Set group by
	 */
	public function group( $data ) {
		if ( is_string($data) ) {
			$this->group = $data;
		} elseif ( is_array($data) ) {
			$this->group = implode(',', $data);
		} else {
			$this->group = '';
		}
		return $this;
	}
	
	/**
	 * Set limit
	 */
	public function limit( $limit ) {
		if ( is_numeric($limit) ) {
			$this->limit = (int)$limit;
		} else {
			$this->limit = 0;
		}
		return $this;
	}
        
        public function having( $data ) {
            if ( is_string($data) ) {
                    $this->having = $data;
            } elseif ( is_array($data) && count($data) ) {
                    $this->having = $this->_where( array( $data ) );
            } else {
                    $this->having = null;
            }
            return $this;
        }
	
	/**
	 * Set offset
	 */
	public function offset( $offset ) {
		if ( is_numeric($offset) && (int)$offset >=0 ) {
			$this->offset = (int)$offset;
		} else {
			$this->offset = 0;
		}
		return $this;
	}

	/**
	 * Set limit and offset
	 * Warning - update limit & offset parameters of sql query
	 * 
	 * @param Int $page		start from 1
	 * @param Int $limit	item per page
	 */
	public function page( $page, $limit ) {
		$this->limit( $limit );
		$this->offset( ($page-1)*$limit );
		return $this;
	}
        
        protected function buildMultiJoins() {
            
            if ( empty($this->multiJoinArray) ) {
                return false;
            }
                        
            foreach( $this->multiJoinArray as $joinInfo ) {
                
                $model1 = null;
                if ( !empty( $joinInfo['model1'] ) ) {
                    $model1 = &$joinInfo['model1'];
                } else {
                    $model1 = &$this->originalModel;
                }
                $table2name = null;
                if ( !empty($joinInfo['table2name']) ) {
                    $table2name = &$joinInfo['table2name'];
                } else {
                    $table2name = &$this->originalModel->name;
                }
                $type = &$joinInfo['type'];
                
                if ( !( isset($model1->foreign) && is_array($model1->foreign) && count($model1->foreign) ) ) {
                    throw new Exception(__CLASS__.'->'.__METHOD__.': multijoin - model1 foreign key(s) is undefined');
                    return false;
                }
                
                $foreign = &$model1->foreign;
                
                if ( !isset($foreign[ $table2name ]) || count($foreign[ $table2name ]) == 0 ) {
                    
                    throw new Exception(__CLASS__.'->'.__METHOD__.': multijoin - not found foreign keys description for model2');
                    return false;
                }                
                
                $condition = array();
                if ( !empty($joinInfo['condition']) ) {
                    $condition = $joinInfo['condition'];
                } else {
                    $foreignInfo = $foreign[ $table2name ];
                    if ( count($foreignInfo) ) {
                        foreach( $foreignInfo as $myKeyName => $info ) {
                            $condition[ $myKeyName ] = new K_Db_Expr( K_Db_Quote::quoteKey($info['key']) );
                        }
                    }                
                }
                
                $this->_join( $table2name, $condition, $type );
            }
            
        }
	
        protected function buildJoins() {

            if ( count($this->joins) ) {
                unset($this->joinTables);
                $this->joinTables = array();
                foreach( $this->joins as &$joinInfo ) {
                    $sql = $joinInfo['type'].' join ';
                    $alias = '';
                    $tableName = '';
                    if ( is_string($joinInfo['table']) ) {
                            $tableName = $joinInfo['table'];
                    } elseif ( is_array($joinInfo['table']) && count($joinInfo['table']) ) {
                            $keys = array_keys($joinInfo['table']);
                            $alias = $keys[0];
                            $tableName = $joinInfo['table'][ $alias ];
                    }

                    $sql .= K_Db_Quote::quoteKey($tableName);

                    if ( !empty($joinInfo['condition']) ) {
                            $sql .= ' on ';

                            $where = '';
                            if ( is_string($joinInfo['condition']) ) {
                                    $where = '('.$joinInfo['condition'].')';
                            } elseif ( is_array($joinInfo['condition']) ) {
                                    $where = '('.$this->_where( array( $joinInfo['condition'] ) ).')';
                            } else {
                                    $where = '(1=1)';
                            }

                            $sql .= $where;

                            if ( !empty($alias) ) {
                                    $sql .= ' as '.K_Db_Quote::quote($alias);
                            }
                    } else {
                            $sql = ', '.K_Db_Quote::quoteKey($tableName);
                    }

                    $this->joinTables[] = array( 'sql' => $sql, 'type' => 'join', 'name'=>$tableName );
                }
            }
        }
        
	/**
	 * Generate SQL select query
	 */
	public function sql() {
            
            	$sql = 'SELECT ';
		
		if ( !empty($this->fields) ) {
                    $sql .= $this->fields;
		} else {
                    $sql .= '*';
		}
		
		if ( empty($this->table) ) {
                    throw('K_Db_Select: table name is empty');
		}
		
                if ( count($this->table) ) {
                    $joinsSql = '';
                    
                    $currentTables = $this->table;
                    
                    $this->buildMultiJoins();
                    
                    if ( count($this->joins) ) {                        
                        $this->buildJoins();
                        $currentTables = array_merge( $this->table, $this->joinTables );
                    }
                    
                    foreach( $currentTables as &$tableInfo ) {
                        if ( $tableInfo['type'] == 'table' ) {
                            $joinsSql .= (!empty($joinsSql)?',':'').$tableInfo['sql'];
                        } elseif ( $tableInfo['type'] == 'join' ) {
                            $joinsSql = '('.$joinsSql.')' . $tableInfo['sql'];
                        } elseif ( $tableInfo['type'] == 'select' ) {
                            $joinsSql .= (!empty($joinsSql)?',':'')
                                      .'('.$tableInfo['sql'].')'
                                      .(!empty($tableInfo['name']) ? ' as '.K_Db_Quote::quoteKey($tableInfo['name']) : '');
                        }
                    }

                    $sql .= ' FROM '.$joinsSql;
		} 
		
		if ( !empty($this->where) ) {
			$sql .= ' WHERE '.$this->where;
		}
		
		if ( !empty($this->group) ) {
			$sql .= ' GROUP BY '.$this->group;
		}
                
        if ( !empty($this->having) ) {
			$sql .= ' HAVING '.$this->having;
		}
		
		if ( !empty($this->order) ) {
			$sql .= ' ORDER BY '.$this->order;
		}
		
		if ( !empty($this->limit) ) {
			$sql .= ' LIMIT '.$this->limit;
		}
		
		if ( !empty($this->offset) ) {
			$sql .= ' OFFSET '.$this->offset;
		}
		
		return $sql.'';
	}
	
	/**
	 * toString
	 * @return <string>
	 */
	public function __toString() {
		return $this->sql();
	}

	/**
	 * Get compiled sql WHERE
	 * @return <string>
	 */
	public function getCompiledWhere() {
		return $this->where;
	}
}

?>
