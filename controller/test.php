<?php
	require "init.php";
	$skill = Skill::getSkillById(6);
	$skill->updateStrength(3, 6, 150);
?>