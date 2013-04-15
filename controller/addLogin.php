<?php
	require "init.php"; 
	$userid = $_GET['userid'];
	$serviceid = $_GET['serviceid'];
	$token = "ABCV";
	$expiredAt = "2003-1-3T00:00:00";

	$user = User::getUserById($userid);
	$user->addLogin($serviceid, $token, $expiredAt);
?>