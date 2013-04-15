<?php
	require "init.php";
	$userid = $_GET['user_id'];
	$skillid = $_GET['skill_id'];
	$user = User::getUserById($userid);
	$ret = $user->addSkill($skillid);
	echo $ret;
?>