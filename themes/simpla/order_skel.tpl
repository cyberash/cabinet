<!-- BEGIN: main --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Multicabinet | {TITLE}</title>
		<!-- Reset Stylesheet -->
		<link rel="stylesheet" href="themes/simpla/css/reset.css" type="text/css" media="screen" />

		<!-- Main Stylesheet -->

		<link rel="stylesheet" href="themes/simpla/css/orderstyle.css" type="text/css" media="screen" />

		<!-- JQuery UI Stylesheet -->
		<link type="text/css" href="themes/simpla/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" />

		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="themes/simpla/css/invalid.css" type="text/css" media="screen" />

		<!--                       Javascripts                       -->

		<!-- jQuery -->
		<script type="text/javascript" src="themes/simpla/js/jquery-1.4.2.min.js"></script>
		
		<!-- jQuery Configuration -->
		<script type="text/javascript" src="themes/simpla/js/simpla.jquery.configuration.js"></script>
	</head>

	<body onLoad="checkCookiep()">
		<div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->


		<div id="main-content"> <!-- Main Content Section with everything -->

			<noscript> <!-- Show a notification if the user has disabled javascript -->
				<div class="notification error png_bg">
					<div>
						Javascript is disabled or is not supported by your browser. Please <a href="http://browsehappy.com/" title="Upgrade to a better browser">upgrade</a> your browser or <a href="http://www.google.com/support/bin/answer.py?answer=23852" title="Enable Javascript in your browser">enable</a> Javascript to navigate the interface properly.
					</div>
				</div>
			</noscript>

			<div class="clear"></div> <!-- End .clear -->
			<div class="clear"></div> <!-- End .clear -->
<!-- BEGIN: productlist -->
	{FILE "themes/simpla/objects/productlist.tpl"}
<!-- END: productlist -->

<!-- BEGIN: orderpkg -->
	{FILE "themes/simpla/objects/orderpkg.tpl"}
<!-- END: orderpkg -->		
			<div class="clear"></div>

		</div> <!-- End #main-content -->

	</div></body>


</html>
<!-- END: main -->
