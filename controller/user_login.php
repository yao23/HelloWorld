<?php
	require "init.php";
	
	if(isset($_POST['email']) && isset($_POST['password'])){
		$email = $_POST['email'];
		$password = $_POST['password'];
		if($user = User::login($email, $password)){
                        $_SESSION['user']=$user;
			header("location:/helloworld/profile.php?uid=".$user->getUid());
			// print 'login succeed!';
		}else{
			print 'login failed';
		}
	}
	

?>