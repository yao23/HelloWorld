<?php 
	require "controller/init.php";
	if(isset($_GET['uid'])){
		$user = User::getUserById($_GET['uid']);
	}else{
		$user = $_SESSION['user'];
	}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Language" content="en-us">
	<title>Hello World</title>
	<link rel="stylesheet" href="css/profile.css" />
	<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
	<script src="http://d3js.org/d3.v3.min.js"></script>
	<script>
		$(document).ready(function(){
			$.ajax({
				data:"user_id=<?=$user->getUid()?>",
				type:"GET",
				url:"controller/listSkills.php",
				success: function(msg){
					$('#allskills').html(msg);
				}
			});
		})
		
	</script>
</head>

<body>
<div id="container" >
	<header>
			<div id="logo"><a href="../helloworld/index.php"><img src = "img/logo.png" alt ="title"></a> </div>
			<div id="breadcrumb">
				<ul id="nav">
					<li><a href="#profile">My Profile</a></li>
					<li><a href="#connections">Connections</a></li>		
					<!-- <li><input type="text" class="search-query span3" placeholder="Search">
                		<div class="icon-search"></div></li>	 -->
					<li><i class="icon-search"></i>
						<input type="search" placeholder="Search"></li>			
					<li><a href="#account">My Account</a>
						<ul>
							<li><a href="#">Settings</a></li>
							<li><a href="#">Privacy Policy</a></li>
							<li><a href="#">Terms of Service</a></li>
							<li><a href="#">About Us</a></li>
							<li><a href="/helloworld/controller/user_logout.php">Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</header>
	<div id="importbar">
		<ul id="importicons" >
<!-- 			<li id="text">Import data from:</li> -->
			<li><a href="/helloworld/controller/update_github.php?uid=<?php echo $_GET['uid']?>" ><img src = "img/github.png" alt ="github"></a><div class="caption">GitHub</div></li>
			<li><a href="#" ><img src = "img/linkedin.png" alt ="linkedin"></a><div class="caption">LinkedIn</div></li>
			<li><a href="/helloworld/controller/update_stackoverflow.php?uid=<?php echo $_GET['uid']?>" ><img src = "img/stackoverflow.png" alt ="stackoverflow"></a><div class="caption">Stackoverflow</div></li>
			<li><a href="#" ><img src = "img/coursera.png" alt ="coursera"></a><div class="caption">Coursera</div></li>
			<li><a href="#" ><img src = "img/interviewstreet.png" alt ="interviewstreet"></a><div class="caption">Interviewstreet</div></li>
		</ul>
	</div>

	<div id="infoviz">
		<ul>
			<li><?=$user->getName()?></li> 
			<li>Web Developer</li>
		</ul>
		<div id="hexagon">
			<script src="js/hexagon.js"></script>
			<script>plotHex(<?=$user->hexSkillString()?>)</script>

		</div>
	</div>
	<div id="allskills">
               <ul>
                       <li>hello</li>
                       <li>hello 2</li>
               </ul>
       </div>
       
    <div id="clear"></div>

</div>
	<!-- <footer>All rights reserved.</footer> -->
	
	<script src="js/iconlable.js"></script>
</body>