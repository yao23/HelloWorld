<?php
	require "init.php";

	$UserHasSkill_id = $_GET['UserHasSkill_id'];
	$skill = Skill::getSkillById($UserHasSkill_id);
	$skill->addHexagonSkill();

?>