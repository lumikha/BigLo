<?php
	require 'functions.php';
?>

<html>
<head>
	<title>BigLo</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style-login.css">
	<link rel="stylesheet" type="text/css" href="js/field_trappings/error_msg.css">
</head>
<body>
	<div id="login_box" class="login_container">
		<div class="login_title">
			<!--<span style="color: #FFB30F;">BIG</span><span>LOCO</span>-->
			<span>Welcome to Little Dipper</span>
		</div>
		<div class="login_inner">
			<!--<div class="col-1">
				<div class="row">
					<h2>Log In</h3>
				</div>
			</div>-->
		<?php
			if($found==false) {
				echo '<div class="col-1 error_all">
						<p>Email/Password incorrect.</p>
					  </div>';
			}
		?>
			<div class="col-1">
				<form action="" method="POST" onsubmit="return check_login_fields();">
					<div class="row">
						<!--<label>Email</label>&nbsp;&nbsp;-->
						<label>&nbsp;</label>
						<span class="error_msg hido" id="hido1"><label id="error1"></label></span>
						<input type="text" name="email" id="email" value="<?php if($_POST) { echo $email; } ?>" onkeypress="hideMsgF1()" placeholder="Email">
					</div>
					<div class="row">
						<!--<label>Password</label>&nbsp;&nbsp;-->
						<label>&nbsp;</label>
						<span class="error_msg hido" id="hido2"><label id="error2"></label></span>
						<input type="password" name="password" id="password" onkeypress="hideMsgF2()" placeholder="Password">
					</div>
					<div class="row">
						<div class="login_frgt_pass"><a href="#" onclick="forgot_pass();">Forgot your Password?</a></div>
						<input class="login_sbmt" type="submit" name="login_btn" value="Log In">
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="lostpass_box" class="login_container" hidden>
		<div class="login_title">
			<!--<span style="color: #FFB30F;">BIG</span><span>LOCO</span>-->
			<span>Welcome to Little Dipper</span>
		</div>
		<div class="login_inner">
			<div class="col-1">
				<div class="row">
					<h2>Reset Your Password</h3>
					<p>In order to reset your password, we must send you a link to your email to verify your account. What email address did you sign up with?</p>
				</div>
				<form action="" method="POST" onsubmit="return check_resetpass_fields();">
					<div class="row">
						<!--<label>Email</label>&nbsp;&nbsp;-->
						<label>&nbsp;</label>
						<span class="error_msg hido" id="hido3"><label id="error3"></label></span>
						<input type="text" name="resetpass_email" id="resetpass_email" onkeypress="hideMsgF3()" placeholder="Email">
					</div>
					<div class="row">
						<button class="go_back_login" onclick="go_back_login();">&#8592;Go Back</button>
						<input class="login_sbmt reset_pass_btn" type="submit" value="Reset Password">
					</div>
				</form>
			</div>
		</div>
	</div>


	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/field_trappings/login.js"></script>
</body>
</html>