<?php

class Login{
	private $Login_id;
	private $User_id;
	private $Service_id;
	private $token;
	private $expireAt;

	public function __construct($Login_id, $User_id, $Service_id, $token, $expireAt){
		$this->Login_id 		= $Login_id;
		$this->User_id 			= $User_id;
		$this->$Service_id 		= $Service_id;
		$this->token 			= $token;
		$this->expireAt 		= $expireAt;
	}

	public static function fromRow($row){
		return new Login($row['Login_id'], $row['User_id'], $row['Service_id'], $row['token'], $row['expireAt']);
	}

}

?>