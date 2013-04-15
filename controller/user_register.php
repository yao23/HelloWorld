<?php
	require "init.php";

	$email 			= $_POST['email'];
	$password 		= $_POST['password'];
	$firstName 		= $_POST['firstName'];
	$lastName 		= $_POST['lastName'];

	
	$id = User::register($email, $password, $firstName, $lastName);
	if($id>0){
		header("location:/helloworld/profile.php?uid=".$id);
		
	}else{
		print "Email already existed";
	}
?>