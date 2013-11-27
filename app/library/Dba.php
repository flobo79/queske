<?php

namespace app\library\Dba;

class Dba {
	
	private $connection;
	static private $instance;
	private $verbose = false;
	
	public static function factory($dsn, $username, $password) {
		if (self::$instance) {
			throw new Exception('Database already initialized');
		}
		try {
			self::$instance = new Dba();
			self::$instance->connection = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES utf8"));
		} catch (PDOException $e) {
			throw new Exception('Connection failed: ' . $e->getMessage());
		}
	}
	
	public static function getAdapter() {
		return self::$instance;
	}
	
	public function beginTransaction() {
		$this->connection->beginTransaction();
	}
	
	public function rollback() {
		$this->connection->rollback();
	}
	
	public function commit() {
		$this->connection->commit();
	}
	
	function fetchRow($table, $where = null, $param = null) {
		$sql = "SELECT * FROM $table";

        if (!$param) {
            if(is_array($where)) {
                $qstring = array();
                foreach($where as $k => $v) {
                    $qstring[] = $k." = ".$this->connection->quote($v);
                }
                $where = implode(" AND ", $qstring);

            } else {
                $where = " id = ".$this->connection->quote($where);
            }


        } else {
			$where = str_replace('?', $this->connection->quote($param), $where);
		}

    $sql .= " WHERE $where";
		$result = array();

		foreach ($this->connection->query($sql) as $row) {
			$result = $row;
			break;
		}
		
		return $result;
	}
	
	function query($sql) {
		return $this->connection->query($sql);
	}
	
	function fetchQuery($sql) {
		$result = array();
		foreach ($this->connection->query($sql) as $row) {
			$result = $row;
			break;
		}

		return $result;
	}
	
	
	function quote ($string) {
		return $this->connection->quote($string);
	}
	
	
	function insert($table, $data) {
		
		$keys = array();
		$values = array();
		
		foreach($data as $key => $value) {
		  $keys[] = "`".$key."`";
		  $values[] = $this->connection->quote($value);
		}
		
		$sql = "INSERT INTO $table (".implode(",", $keys).") VALUES (".implode(",", $values).")";
		
		if($this->verbose) echo $sql;
		$this->connection->query($sql);
	}
	
	
	function lastInsertId () {
		return $this->connection->lastInsertId();
	}
	
	
	function update($table, $data, $where) {
    $sql = "UPDATE ".$table." SET ";
		foreach($data as $field => $value) {
			  $sql .= '`'.$field.'`' . ' = ' . $this->connection->quote($value).', ';
		}
	  
	  $sql = trim($sql, ', ').' WHERE '.$where;
		if($this->verbose) echo $sql;
		return $this->connection->query($sql);
	}


	function fetchAll($table, $query) {
      $result = false;
  		$sql = "SELECT * FROM $table";
  		if ($query) { $sql .= " where ".$query; }

      $q = $this->connection->query($sql); {
      $result = $q->fetchAll(PDO::FETCH_OBJ);
		}
		if($this->verbose)  echo $sql;

		return $result;
	}
	
	function delete($table, $id) {
		$sql = sprintf("DELETE FROM $table where id=%d", $id);

		if($this->verbose) echo $sql; 
		$result = $this->connection->query($sql);
		return $result->rowCount();
	}
	
	function setVerbose($set) {
		$this->verbose = $set;	
	}
	
	private function __construct() {}
}