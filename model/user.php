<?php

class User{
	private $id;
	private $email;
	private $password;
	private $firstName;
	private $lastName;
	private $skills;
	private $logins;
	private $hexSkills = array();

	public function __construct($id, $email, $password, $firstName, $lastName){
		$this->id 			= (int)$id;
		$this->email 		= $email;
		$this->password 	= $password;
		$this->firstName 	= $firstName;
		$this->lastName 	= $lastName;
		$this->skills		= $this->querySkills();
		$this->logins		= $this->queryLogins();
	}

	//another constructor, create user from a row of result; 
	public static function userFromRow($row){
		return new User($row["User_id"], $row['email'], $row['password'], $row['firstName'], $row['lastName']);
	}

	public static function getUserById($id){
		$database = new Database();
		$conditions = array(
			"User_id" => $id
		);
		$res = $database->select('USER', $conditions);
		return User::userFromRow($res[0]);
	}
        
        public function getUid(){
            return $this->id;
        }

        public static function login($email, $password){
		$database = new Database();
		$conditions = array(
			"email" => $email,
			"password" => $password,
		);
		$res = $database->select('USER', $conditions);
		$row = $res[0];
		$user = User::userFromRow($row);

		if(count($res)>0){
			session_start();
			$_SESSION['user'] = $user;
			return $user;
		}

		return null;
	}

	public static function register($email, $password, $firstName, $lastName){
		$database = new Database();
		$data = array(
				'email'=>$email,
				'password'=>$password,
				'firstName'=>$firstName,
				'lastName'=>$lastName
			);
		$res = $database->select('USER', $data);
		if(count($res)>0){
			return -1;
		}
		$id = $database->insert('USER', $data);
		session_start();
		$_SESSION['user'] = User::getUserById($id);
		return $id;
	}

	

	public function isFollowing($id){
		$database = new Database();
		$conditions = array(
			'fromUser'	=>$this->id,
			'toUser'	=>$id
			);
		$res = $database->select('FOLLOW', $conditions);
		if(count($res)>0){
			return 1;
		}else{
			return 0;
		}
	}

	public function isFollowedBy($id){
		$database = new Database();
		$conditions = array(
			'fromUser'	=>$id,
			'toUser'	=>$this->id
			);
		$res = $database->select('FOLLOW', $conditions);
		if(count($res)>0){
			return 1;
		}else{
			return 0;
		}
	}

	public function listFollowing(){
		$database = new Database();
		$conditions = array(
			'fromUser' => $this->id
		);
		$res = $database->select('FOLLOW', $conditions);
		$rows = array();
		foreach($res as $row){
			$user = User::getUserById($row['toUser']);
			array_push($rows, $user);
		}


		return $rows;
	}

	public function listFollower(){
		$database = new Database();
		$conditions = array(
			'toUser' => $this->id
		);
		$res = $database->select('FOLLOW', $conditions);
		$rows = array();
		foreach($res as $row){
			$user = User::getUserById($row['toUser']);
			array_push($rows, $user);
		}

		return $rows;
	}

	public function toJSON(){
		return json_encode(array(
			'id' 		=> $this->id,
			'email' 	=> $this->email,
			'firstName' => $this->firstName,
			'lastName' 	=> $this->lastName
		));
	}

	public function querySkills(){
		$database = new Database();
		$conditions = array(
			'User_id' => $this->id
		);
		$res = $database->select('USER_has_SKILL', $conditions);
		$skills = array();
		foreach($res as $row){
			$skill = Skill::fromRow($row);
			if(strlen($skill->getName())==0){
				continue;
			}
			if($skill->getStrength()==0){
				$skill->removeHexagonSkill();
			}else{
				$skill->addHexagonSkill();
			}
			array_push($skills, $skill);
		}
		$this->hexSkills = array();
		foreach($skills as $skill){
			if($skill->getIsInHexagon()){
				array_push($this->hexSkills, $skill);
			}
		}


		return $skills;
	}

	public function hexSkillString(){
		$string = '';
		for($i=0; $i<6; $i++){
			if(!is_object($c = current($this->hexSkills))){
				$string .= "'Unset',0,";
			}else{
				$string .= "'".$c->getName()."',".$c->getStrength(). ',';
			}
			next($this->hexSkills);
		}
		$string = substr_replace($string ,"",-1);
		return $string;
	}



	private function queryLogins(){
		$database = new Database();
		$conditions = array(
			'User_id'=>$this->id
		);
		$res = $database->select('LOGIN', $conditions);
		$logins = array();
		foreach($res as $row){
			$login = Login::fromRow($row);
			array_push($logins, $login);
		}
		return $logins;
	}

	public function addSkill($skill_id){
		$database = new Database();
		$conditions = array(
			'User_id'=>$this->id,
			'Skill_id'=>$skill_id,
		);
		$res = $database->selectOne('USER_has_SKILL', $conditions);

		if(count($res)>0){
			return -1;
		}else{
			$data = array(
				'User_id'=>$this->id,
				'Skill_id'=>$skill_id,
				'strength'=>0,
				'isInHexagon'=>0,
			);
			$res = $database->insert('USER_has_SKILL', $data);
			return $res;
		}

	}

	public function addLogin($Service_id, $token, $expireAt){
		$database = new Database();
		$conditions = array(
			'User_id'=>$this->id,
			'Service_id'=>$Service_id
		);
		$data = array(
			'token' => $token,
			'expireAt' => $expireAt
		);
		$res = $database->selectOne('LOGIN', $conditions);
		if(count($res)>0){
			$database->update('LOGIN', $data, $conditions);
		}else{
			$data = $conditions+$data;
			$database->insert('LOGIN', $data);
		}

	}

	public function isExpired($Service_id){
		$database = new Database();
		$conditions = array(
			'User_id'=>$this->id,
			'Service_id'=>$Service_id
		);
		$res = $database->selectOne('LOGIN', $conditions);
		if(count($res)>0){
			return (time()>strtotime($res["expireAt"]));
		}else{
			return true;
		}

	}

	
	
	//perporties
	public function getId()		{ return $this->id; }
	public function getName()	{ return $this->firstName.' '.$this->lastName; }
	public function getSkills() { return $this->skills; }

}

?>