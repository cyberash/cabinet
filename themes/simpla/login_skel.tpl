<!-- BEGIN: main --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Multicabinet | </title>
		<!--                       CSS                       -->
		<!-- Reset Stylesheet -->
		<link rel="stylesheet" href="themes/simpla/css/reset.css" type="text/css" media="screen" />
		<!-- Main Stylesheet -->
		<link rel="stylesheet" href="themes/simpla/css/style.css" type="text/css" media="screen" />
		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="themes/simpla/css/invalid.css" type="text/css" media="screen" />

		<script type="text/javascript" src="themes/simpla/js/jquery-1.3.2.min.js"></script>
	<!-- jQuery Configuration -->
		<script type="text/javascript" src="themes/simpla/js/simpla.jquery.configuration.js"></script>
	</head>

	<body id="login">

		<div id="login-wrapper" class="png_bg">
			<div id="login-top">
				<img id="logo" src="themes/simpla/images/logo.png" alt="Simpla Admin logo" />

			</div> <!-- End #logn-top -->

			<div id="login-content">

				<form action="{PHP._SERVER.PHP_SELF}" method="POST">
<!-- BEGIN: attention -->
			<div class="notification attention png_bg">
				<div>
					{ATTENTIONMSG}
				</div>
			</div>
<!-- END: attention -->
					<p>

						<label>Username</label>
						<input name="loginusername" class="text-input" type="text" />
					</p>
					<div class="clear"></div>
					<p>
						<label>Password</label>
						<input name="loginpassword" class="text-input" type="password" />
					</p>

					<div class="clear"></div>
					<p id="remember-password">
						<input type="checkbox" />Remember me
					</p>
					<div class="clear"></div>
					<p>
						<input class="button" type="submit" value="Sign In" />
					</p>

				</form>

			</div> <!-- End #login-content -->

		</div> <!-- End #login-wrapper -->

  </body>

</html>
<!-- END: main -->
