<?php
	require "init.php";

	if(!isset($_SESSION['user'])){
		//user not log in --> redirect to login
		print "Error: not logged in yet";
	}else{
		$user = User::getUserById($_SESSION['user']);
		$following = $user->listFollowing();
		
	}

?>
{ "followings":[
<?php 
	$i=0;
	foreach($following as $f){
		echo $f->toJSON();
		$i++;
		if($i<count($following)){
			echo ',';
		}
	}
?>
]}