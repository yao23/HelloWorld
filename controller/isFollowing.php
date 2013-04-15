<?php
	require "init.php";

	if(isset($_GET['from']) && isset($_GET['to'])){
		$from 	= $_GET['from'];
		$to 	= $_GET['to'];
		$fromUser = User::getUserById($from);
		$ret = $fromUser->isFollowing($to);
		echo $ret;
	}else{
		echo "Error: missing parameter";
	}
?>