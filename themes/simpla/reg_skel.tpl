<!-- BEGIN: main --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Multicabinet | Dashboard</title>

		<!--                       CSS                       -->

		<!-- Reset Stylesheet -->
		<link rel="stylesheet" href="themes/simpla/css/reset.css" type="text/css" media="screen" />

		<!-- Main Stylesheet -->

		<link rel="stylesheet" href="themes/simpla/css/orderstyle.css" type="text/css" media="screen" />

		<!-- JQuery UI Stylesheet -->
		<link type="text/css" href="themes/simpla/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" />

		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="themes/simpla/css/invalid.css" type="text/css" media="screen" />

		<!-- Colour Schemes

		Default colour scheme is green. Uncomment prefered stylesheet to use it.

		<link rel="stylesheet" href="css/blue.css" type="text/css" media="screen" />

		<link rel="stylesheet" href="css/red.css" type="text/css" media="screen" />

		-->

		<!--                       Javascripts                       -->

		<!-- jQuery -->
		<script type="text/javascript" src="themes/simpla/js/jquery-1.4.2.min.js"></script>
		


		<!-- jQuery Configuration -->
		<script type="text/javascript" src="themes/simpla/js/simpla.jquery.configuration.js"></script>

		<!-- JQuery UI (Datepicker) -->
		<script type="text/javascript" src="themes/simpla/js/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="themes/simpla/js/cookie.js"></script>
		
<script type="text/javascript">

$(document).ready(function()
{

$("#username").change(function()
{
var username = $("#username").val();

if(username.length > 3){
	var msgbox = $("#namestatus");
$("#namestatus").html('<img src="/themes/simpla/images/loading.gif"/>&nbsp;Checking availability.');

$.ajax({
cache: false,
type: "GET",
url: "ajax.php", 
data: "username="+ username, 
success: function(msg){
$("#namestatus").ajaxComplete(function(event, request){

if(msg == '1')
{
$("#namestatus").removeClass("error"); // remove red color
$("#namestatus").addClass("success"); // add green color
msgbox.html('Available');
} 
else 
{
// if you don't want background color remove these following two lines
$("#namestatus").removeClass("success"); // remove green color
$("#namestatus").addClass("error"); // add red  color
msgbox.html('Not Available');
} 
});
}
});

} else {
	var msgbox = $("#namestatus");
	$("#namestatus").removeClass("error");
	$("#namestatus").removeClass("success");
	$("#namestatus").addClass("information");
	msgbox.html('Please, give at least 3 characters for username');
}
//return false;
});

$("#password").change(function()
{
	var password = $("#password").val();
	var msgbox = $("#pwdstatus");
if(password.length < 5){
	msgbox.removeClass("success");
	msgbox.addClass("error");
	msgbox.html('Please, give at least 5 characters for password');
} else {
	msgbox.removeClass("error");
	msgbox.addClass("success");
}
});
$("#password2").change(function()
{
	var password = $("#password").val();
	var password2 = $("#password2").val();
	var msgbox = $("#pwd2status");
if(password == password2){
	msgbox.removeClass("error");
	msgbox.addClass("success");
} else {
	msgbox.removeClass("success");
	msgbox.addClass("error");
	msgbox.html("Passwords should match!");
}
});

$("#email").change(function()
{
	var email = $("#email").val();
	var msgbox = $("#emailstatus");
	$("#namestatus").html('<img src="/themes/simpla/images/loading.gif"/>&nbsp;Checking...');

	$.ajax({
cache:false,
type: "GET",
url: "ajax.php",
data: "email="+email,
success: function(msg){
$("#emailstatus").ajaxComplete(function(event, request){

if(msg == '1')
{
$("#emailstatus").removeClass("error"); // remove red color
$("#emailstatus").addClass("success"); // add green color
} 
else 
{
// if you don't want background color remove these following two lines
$("#emailstatus").removeClass("success"); // remove green color
$("#emailstatus").addClass("error"); // add red  color
msgbox.html('Please, specify correct email');
} 
});
}
		});
});


});
</script>
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
<!-- BEGIN: regform --> 
	{FILE "themes/simpla/objects/regform.tpl"}
<!-- END: regform --> 
<!-- BEGIN: regsuccess --> 
	{FILE "themes/simpla/objects/regsuccess.tpl"}
<!-- END: regsuccess --> 
			</div>
			</div>
			
			<div class="clear"></div>

		</div> <!-- End #main-content -->

	</div></body>


</html>
<!-- END: main -->
