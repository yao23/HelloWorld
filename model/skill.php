<?php
	
	class Skill{

		private $UserHasSkill_id;
		private $User_id;
		private $id;
		private $name;
		private $strength;
		private $isInHexagon;
		private $proofItems = array();
                //private static $database_static = new Database();


        public function __construct($UserHasSkill_id, $User_id, $id, $strength, $isInHexagon){
			$this->UserHasSkill_id 	= $UserHasSkill_id;
			$this->User_id 			= $User_id;
			$this->id 				= $id;
			if($strength>1){
				$this->strength 	= 1;
			}else{
				$this->strength 	= $strength;
			}
			$this->isInHexagon 		= $isInHexagon;

			$this->querySkill();
			$this->queryProofItems();
		}

		public static function getSkillById($UserHasSkill_id){
			$database = new Database();
			$conditions = array('UserHasSkill_id'=>$UserHasSkill_id);
			$row = $database->selectOne('USER_has_SKILL', $conditions);
			return Skill::fromRow($row);

		}
                
                //get user_has_skill_id, if not exist, insert one
                public static function getUserSkillById($skill_id,$uid){
                    $database = new Database();
		    $conditions = array('Skill_id'=>$skill_id,'User_id'=>$uid);
	            $row = $database->selectOne('USER_has_SKILL', $conditions);
                    if(count($row)>0)
                        return Skill::fromRow($row)->UserHasSkill_id;
                    else
                        return $database->insert('USER_has_SKILL', array('Skill_id'=>$skill_id,'User_id'=>$uid, 'isInHexagon'=>1));
                }
                
                //get skill id, if not exist, insert one
                public static function getSkillByName($name){
                    $database = new Database();
		    $conditions = array('name'=>$name);
	            $row = $database->selectOne('SKILL', $conditions);
                    if(count($row)>0)
                        return $row['Skill_id'];
                    else{
                        return $database->insert('SKILL', array('name'=>$name));
                    }
                }
                
		public static function fromRow($row){
			return new Skill($row['UserHasSkill_id'], $row['User_id'], $row['Skill_id'], $row['strength'], $row['isInHexagon']);
		}

		public function toJSON(){
			return json_encode(array(
				'UserHasSkill_id' => $this->UserHasSkill_id,
				'User_id'=> $this->User_id,
				'id' => $this->id,
				'name' => $this->name,
				'strength' => $this->strength,
				'isInHexagon' => $this->isInHexagon
			));
		}

		private function querySkill(){
			$database = new Database();
			$conditions = array(
				'Skill_id' => $this->id
			);

			$row = $database->selectOne('SKILL', $conditions);
			$this->name = $row['name'];
		}

		public function queryProofItems(){
			$database = new Database();
			$conditions = array(
				'UserHasSkill_id' => $this->UserHasSkill_id
			);
			$res = $database->select('PROOFITEM', $conditions);

			foreach($res as $row){
				$proofItem = new ProofItem($row['ProofItem_id'], $row['Proof_id'], $row['UserHasSkill_id'], $row['amount']);
				array_push($this->proofItems, $proofItem);
			}
		}

		public function updateStrength($Proof_id, $UserHasSkill_id, $amount){
			/*
				Before entering this function, you should have the following parameters:
					1. Which service your are updating.
					2. Which kind of proof you are updating.
					3. Whose data you what to update,
					4. what skill of that person you should update.
				By having that 4 things in mind, you should be easy to get $Proof_id, $Skill_id
				The logic is like this,
					1. QUERY previous proofitems WHERE UserHasSkill_id = $this->UserHasSkill_id AND Proof_id = $Proof_id
					2. if not exist -> insert one;
					3. if exist ->update it;
					4. query all the proofitems
					5. calculate the new strength 
			*/

			$database = new Database();
			$conditions = array(
				'Proof_id' => $Proof_id,
				'UserHasSkill_id' => $UserHasSkill_id
			);
			$row = $database->selectOne('PROOFITEM', $conditions);
			if(count($row)>0){

				//update
				$data = array('amount' => $amount);
				$database->update('PROOFITEM', $data, $conditions);
			}else{
				$data = $conditions;
				$data['amount'] = $amount;
				//insert
				$database->insert('PROOFITEM', $data);
			}

			$this->proofItems = array();
			$this->queryProofItems();

			$this->calculateStrength();

		}

		public function calculateStrength(){
			$strength = 0;
			foreach($this->proofItems as $pi){
				$strength += ($pi->getAmount())*($pi->getWeight());
			}
			$this->strength = $strength;
			$database = new Database();
			$data = array('strength'=>$strength);
			$conditions = array('UserHasSkill_id'=>$this->UserHasSkill_id);
			$database->update('USER_has_SKILL', $data, $conditions);
		}

		public function addHexagonSkill(){
			$database = new Database();
			$data = array('isInHexagon'=>1);
			$conditions = array('UserHasSkill_id'=>$this->UserHasSkill_id);
			$database->update('USER_has_SKILL', $data, $conditions);

		}

		public function removeHexagonSkill(){
			$database = new Database();
			$data = array('isInHexagon'=>"0");
			$conditions = array('UserHasSkill_id'=>$this->UserHasSkill_id);
			$database->update('USER_has_SKILL', $data, $conditions);
		}

		public function getIsInHexagon(){return $this->isInHexagon;}

		public function getName() {return $this->name;}
		public function getStrength(){return $this->strength;}


	}
?>