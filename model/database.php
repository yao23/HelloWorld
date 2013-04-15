<?php

class Database{
	private $database	= 'evente9_helloworld';
	private $username	= 'evente9_hw';
	private $password 	= 'UBHacking2013';
	private $host 		= '205.134.224.227';
	private $mysqli;
	private $result;
	private $insert_id;

	public function __construct(){
		$this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->database);
	}

	public function query($sql){
		$this->result = null;
		if(!$this->result = $this->mysqli->query($sql)){
			print 'Error: when executing sql query "'.$sql.'" Error No.: "'.$this->mysqli->errno.'"';
		}else{
			$this->insert_id = $this->mysqli->insert_id;
			$this->mysqli->commit();
			
		}
	}

	public function select($table, $conditions){
		$sql = 'SELECT * from `'.$table.'` WHERE ';
		for($i=0;$i<count($conditions);$i++){
			$c = current($conditions);
			$sql .= '`'.key($conditions).'` = \''.$c.'\' ';
			next($conditions);
			if($i<(count($conditions)-1)){
				$sql .= 'AND ';
			}
		}
		$this->query($sql);
		if(is_object($this->result)){
			return $this->fetchAll();
		}else{
			return array();
		}
	}

	public function selectOne($table, $conditions){
		$res = $this->select($table, $conditions);
		if(count($res)>0){
			return $res[0];
		}else{
			return array();
		}
	}

	public function insert($table, $data){
		$sql = 'INSERT INTO `'.$table.'` (';
		for($i=0; $i<count($data); $i++){
			$c = current($data);
			$sql .= '`'.key($data).'`';
			next($data);
			if($i<count($data)-1){
				$sql .= ', ';
			}
		}
		$sql .= ')VALUES ( ';
		reset($data);
		for($i=0; $i<count($data);$i++){
			$c = current($data);
			$sql .= "'".$c."'";
			next($data);
			if($i<count($data)-1){
				$sql .= ', ';
			}
		}
		$sql .= ")";
		$this->query($sql);
		return $this->insert_id;
	}

	public function update($table, $data, $conditions){
		
		$sql = 'UPDATE `'.$table.'` SET ';
		
		for($i=0;$i<count($data);$i++){	
			$c = current($data);
			$sql .= '`'.key($data).'` = \''.$c.'\'';
			next($data);
			if($i<count($data)-1){
				$sql .= ', ';
			}
		}
		
		$sql .= ' WHERE ';
		for($i=0;$i<count($conditions);$i++){
			$c = current($conditions);
			$sql .= '`'.key($conditions).'` = \''.$c.'\'';
			next($conditions);
			if($i<count($conditions)-1){
				$sql .= 'AND ';
			}
		}
		$this->query($sql);
	}

	private function fetchAll(){
		$rows = array();
		while($row = $this->result->fetch_assoc()){
			array_push($rows,$row);
		}
		return $rows;
	}
}

?>