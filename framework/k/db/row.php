<?php 

class K_Db_Row implements Iterator, ArrayAccess {
    protected $model;

    protected $data;

    public function __construct( $data = null, &$model = null ) {
        if ( is_array($data) ) {
                $this->data = $data;
        }
        if ( $model instanceof K_Db_Model ) {
                $this->model = &$model;
        }
    }

    function rewind() {
        return reset($this->data);
    }

    function current() {
        return current($this->data);
    }

    function key() {
        return key($this->data);
    }

    function next() {
        return next($this->data);
    }

    function valid() {
        return key($this->data) !== null;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
	
	public function save( $update = null ) {		
		if ( !empty($this->model) ) {			
			if ( $update instanceof K_Db_Row ) {				
				$this->data = array_merge( $this->data, $update->toArray() );
			} elseif ( is_array($update) ) {
				$this->data = array_merge( $this->data, $update );
			}
			$this->model->save( $this );
		}
	}

	public function remove() {
		if ( !empty($this->model) ) {
			$keyName = $this->model->primary;
			$this->model->remove( K_Db_Select::create()->where( array( $keyName => $this->data[ $keyName ] ) ) );
		}
	}
	
	public function toArray() {
		return $this->data;
	}
}

?>