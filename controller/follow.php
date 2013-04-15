<?php
	require "init.php";

	if(!isset($_SESSION['user'])){
		//user not log in --> redirect to login
		print "not logged in yet";
	}else{
		$fromUserID = ($_SESSION['user']);
		
		if(!isset($_GET['user_id'])){
			//not set whom to follow --> redirect to 404
			print "whom to follow?";
		}else{
			$toUserID = ($_GET['user_id']);

			$fromUser = User::getUserById($fromUserID);
			$toUser   = User::getUserById($toUserID);


			$database = new Database();
			$data = array(
				"fromUser" => $fromUserID,
				"toUser" => $toUserID
			);
			$rows = $database->select('FOLLOW', $data);
			if(count($rows)>0){
				# already friends
				print "you are already friends";
			}else{
				$ret = $database->insert('FOLLOW', $data);
				var_dump($ret);
				if($ret){
					print $fromUser->getName()." successfully followed ".$toUser->getName(); 
				}
			}
		}
	}
	

?>
