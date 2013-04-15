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
	<link rel="stylesheet" href="/helloworld/css/signup.css" />
</head>

<body>
	<div id="container">
		<header>
			<div id="logo"><a href="../helloworld/index.php"><img src = "img/logo.png" alt ="title"></a> </div>
			<div id="nav">
				<ul>
					<li><a href="#about">About</a></li>
					<li><a href="../helloworld/login.php">Login</a></li>
				</ul>
			</div>
		</header>

		<div id="signup">
				<form class="form-signup" action="/helloworld/controller/user_register.php" method="post">
					<p class="field">
						<input type="text" name="firstName" placeholder="First Name">
						<i class="icon-user icon-large"></i>

						<input type="text" name="lastName" placeholder="Last Name">
						<!-- <i class="icon-circle icon-large"></i> -->
					</p>
					<p class="field">
						<input type="text" name="email" placeholder="Email">
						<i class="icon-envelope icon-large"></i>
					</p>
						<p class="field">
							<input type="password" name="password" placeholder="Password">
							<i class="icon-lock icon-large"></i>
					</p>
					<p class="submit">
						<button type="submit" name="submit"><i class="icon-arrow-right icon-large"></i></button>
					</p>
				</form>
			
		</div>
	</div>
		<footer>All rights reserved.</footer>
</body>