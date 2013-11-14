<?php

/**
 *
 * Class BaseModel
 */
class BaseModel {
	
	private $requiresSession = true;
	private $identity = false;

	function BaseModel($table) {
        $this->table = $table;
        //$this->identity = $_SESSION['identity'];
	}
	
	function getDB() {
		return Dba::getAdapter();
	}

    public function getIdentity() {

      if(emtpy($_SESSION['identity'])) {
          return false;
      }

      return $_SESSION['identity'];
    }
    

    /**
     * finds a record by an id, a field, value combination or a set of parameters
     *
     * @param $id
     * @param string $field 
     * @return array
     */
    function get($id, $field=false) {
        if(!$field) $field = "id";
        if(is_array($id)) {
            return $this->getDB()->fetchRow($this->table, $id);
        }
        return $this->getDB()->fetchRow($this->table, $field.' = ?', $id);
    }
        
        
    function getAll($query="") {
        return $this->getDB()->fetchAll($this->table, $query);
    }


    /**
     *
     * @param $data
     * @param $id
     * @param string $key
     */
    function update ($data, $id=false, $key="id") {
        $set = array();
        
        foreach($this->getProperties() as $k) {
            $k = $k->name;
            if(!empty($data[$k])) {
              $set[$k] = $data[$k];
              
              if($k == "id" && !$id) {
                $id = $data[$k];
              } else {
                unset($set['id']);
              }
            }
        }
        
        unset($set['table']);
        $this->getDb()->update($this->table, $set, " $key = $id LIMIT 1");
    }
    
    
     /**
     *
     * @param $data
     * @param $id
     * @param string $key
     */
    function insert($data) {
        $set = array();
        
        foreach($data as $k => $v) {
            if(property_exists($this, $k)) {
                $set[$k] = $v;
                $this->$k = $v;
            }
        }
        
        $db = $this->getDb();
        $db->insert($this->table, $set);

        if($id = $db->lastInsertId()) {
            $this->setId($id);
            return $this;
        }
        
        return false;
    }
    
    public function setId($id) {
      $this->id = $id;
    }
    
    
  	function delete() {
  		  return $this->db->delete($this->table, $this->id);
  	}
  	
  	
    
    /**
     * returns a sanitizes string
     *
     * @param $str
     * @return string
     */
  	public function _sanitzeString($str) {
        $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
        $str = str_replace($special_chars, '', $str);

        $find = Array('/[\s-]+/', "/�/","/�/","/�/","/�/","/�/","/�/","/�/","/JPG/");
        $replace = Array('-', "ae","oe","ue","Ae","Oe","Ue","ss","jpg");
        $str = preg_replace($find, $replace, $str);
        return trim($str, '.-_');
    }
    
  	
  	public function getId() {
        return $this->id;
    }
    
    
    function getTable() {
      return $this->table;
    }
    
    function setTable($table) {
      return $this->table;
    }
    
    
    public function getData() {
      $c = new ReflectionClass(get_class($this));
      $properties = $this->getProperties();      
      
      if($this->getId()) {
        $data = array();
       
        foreach($properties as $p) {
          $prop = $p->name;
          $data[$prop] = $this->$prop;
        }
      }
      
      unset($data['table']);
      
      return $data;
    }
    
    
    function getProperties() {
      $c = new ReflectionClass(get_class($this));
      return $c->getProperties(
        ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
      );#
    }
    
  
    function duplicate($data, $id=false) {
      
      if($this->id) { 
        $src = $this; 
      } else {
        $src = $this->load($id);
      }

      $c = new ReflectionClass( get_class($src) );
      $properties = $c->getProperties(
        ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
      );
      
      if($src->getId()) {
        $new = array();
       
        foreach($properties as $p) {
          $prop = $p->name;
           $new[$prop] = !empty($data[$prop]) ? $data[$prop] : $src->$prop;
        }
        
        unset($new['id'], $new['table']);
        return $this->insert($new);
      }
      
      return false;
    }
}






