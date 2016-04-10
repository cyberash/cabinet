<!-- BEGIN: main --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Multicabinet {VERSION} | Setup</title>

		<!--                       CSS                       -->

		<!-- Reset Stylesheet -->
		<link rel="stylesheet" href="template/css/reset.css" type="text/css" media="screen" />

		<!-- Main Stylesheet -->

		<link rel="stylesheet" href="template/css/orderstyle.css" type="text/css" media="screen" />

		<!-- JQuery UI Stylesheet -->
		<link type="text/css" href="template/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" />

		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="template/css/invalid.css" type="text/css" media="screen" />

		<!-- Colour Schemes

		Default colour scheme is green. Uncomment prefered stylesheet to use it.

		<link rel="stylesheet" href="css/blue.css" type="text/css" media="screen" />

		<link rel="stylesheet" href="css/red.css" type="text/css" media="screen" />

		-->

		<!--                       Javascripts                       -->

		<!-- jQuery -->
		<script type="text/javascript" src="template/js/jquery-1.4.2.min.js"></script>
		


		<!-- jQuery Configuration -->
		<script type="text/javascript" src="template/js/simpla.jquery.configuration.js"></script>

		<!-- JQuery UI (Datepicker) -->
		<script type="text/javascript" src="template/js/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="template/js/cookie.js"></script>

	</head>

	<body onLoad="checkCookiep()"><div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->


		<div id="main-content"> <!-- Main Content Section with everything -->

			<noscript> <!-- Show a notification if the user has disabled javascript -->
				<div class="notification error png_bg">
					<div>
						Javascript is disabled or is not supported by your browser. Please <a href="http://browsehappy.com/" title="Upgrade to a better browser">upgrade</a> your browser or <a href="http://www.google.com/support/bin/answer.py?answer=23852" title="Enable Javascript in your browser">enable</a> Javascript to navigate the interface properly.
					</div>
				</div>
			</noscript>

			<div class="clear"></div> <!-- End .clear -->
<!-- BEGIN: attention -->
			<div class="notification attention png_bg">
				<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
				<div>
					{ATTENTIONMSG}
				</div>
			</div>
<!-- END: attention -->
<!-- BEGIN: success -->
			<div class="notification success png_bg">
				<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
				<div>
					{SUCCESSMSG}
				</div>
			</div>
<!-- END: success -->
			
			<div class="clear"></div> <!-- End .clear -->
			<div class="content-box">
			
<!-- BEGIN: checks -->
			{FILE "template/checks.tpl"}
<!-- END: checks -->

<!-- BEGIN: pathes -->
			{FILE "template/pathes.tpl"}
<!-- END: pathes -->

<!-- BEGIN: database -->
			{FILE "template/database.tpl"}
<!-- END: database -->

<!-- BEGIN: dbinstall -->
			{FILE "template/dbinstall.tpl"}
<!-- END: dbinstall -->

<!-- BEGIN: addadmin -->
			{FILE "template/addadmin.tpl"}
<!-- END: addadmin -->

			</div>
			</div>
			
			<div class="clear"></div>

		</div> <!-- End #main-content -->

	</div></body>


</html>
<!-- END: main -->
