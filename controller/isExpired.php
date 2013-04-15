<?php
	require "init.php";
	$userid = $_GET['userid'];
	$serviceid = $_GET['serviceid'];

	$user = User::getUserById($userid);
	$ret = $user->isExpired($serviceid);
	var_dump($ret);
?>