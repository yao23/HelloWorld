<?php
	class ProofItem{

		private $ProofItem_id;
		private $Proof_id;
		private $UserHasSkill_id;
		private $amount;
		private $proofName;
		private $weight;
		private $description;
		private $Service_Id;
		private $serviceName;

		public function __construct($ProofItem_id, $Proof_id, $UserHasSkill_id, $amount){
			$this->ProofItem_id 		= $ProofItem_id;
			$this->Proof_id 			= $Proof_id;
			$this->UserHasSkill_id 		= $UserHasSkill_id;
			$this->amount 				= $amount;

			$this->queryProof();
		}

		private function queryProof(){
			$database = new Database();
			$conditions = array('Proof_id'=>$this->Proof_id);
			$row = $database->selectOne('PROOF', $conditions);
			if(count($row)>0){
				$this->name = $row['name'];
				$this->weight = (double)$row['weight'];
				$this->description = $row['description'];
				$this->Service_Id = $row['Service_id'];

				$conditions = array('Service_Id'=>$this->Service_Id);
				$row = $database->selectOne('Service', $conditions);
				$this->serviceName = $row['Service_name'];
			}
		}
                
                //get proof id, if not exist, insert one
                public static function getProofId($service_id,$proof_name){
                        $database = new Database();
			$conditions = array('Service_id'=>$service_id,'name'=>$proof_name);
			$row = $database->selectOne('PROOF', $conditions);
			if(count($row)>0){
                            return $row['Proof_id'];
                        }else
                            return $database->insert ('PROOF', array('Service_id'=>$service_id,'name'=>$proof_name,'weight'=>0.01));
                }

		

		 public function getAmount(){return $this->amount;}
		 public function getWeight(){return $this->weight;}
	}
?>