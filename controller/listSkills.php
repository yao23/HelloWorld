<?php
	require "init.php";

	if(isset($_GET['user_id'])){
		$user = User::getUserById($_GET['user_id']);
		$skills = $user->getSkills();
	}else{
		echo "Error: whose skill?";
	}

?>
<div><i class="icon-th-list icon-large" style="padding:15px;"></i>Skill List</div>
<ul>
<?php
	foreach($skills as $skill){
?>
		<li>

			<span class="skillName"> <?=$skill->getName()?></span>
			<span class="strengthBar"><?=$skill->getStrength()?></span>
		</li>
<?php
	}
?>
</ul>
