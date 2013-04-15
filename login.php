<?php session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Language" content="en-us">
	<title>Hello World</title>
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> -->
	<!-- <link rel="stylesheet" href="http://meyerweb.com/eric/tools/css/reset/reset.css" /> -->
	<link rel="stylesheet" href="/helloworld/css/login.css" />
</head>

<body>
	<div id="container">
		<header>
			<div id="logo"><a href="../helloworld/index.php"><img src = "img/logo.png" alt ="title"></a></div>
			<div id="nav">
				<ul>
					<li><a href="#about">About</a></li>
					<li><a href="#login">Login</a></li>
				</ul>
			</div>
		</header>

		<div id="login">
		<!-- <form name="signup" action="/CLAPP/controller/signup.php" method="post"> -->
				<form class="form-login" action="/helloworld/controller/user_login.php" method="post" data-ajax="false">
					<p class="field">
						<input type="text" name="email" placeholder="Email">
						<i class="icon-envelope icon-large"></i>
					</p>
						<p class="field">
							<input type="password" name="password" placeholder="Password">
							<i class="icon-lock icon-large"></i>
					</p>
					<p class="submit">
						<button type="submit"><i class="icon-arrow-right icon-large"></i></button>
					</p>
				</form>
			
		</div>		
	</div>
		<footer>All rights reserved.</footer>
</body>