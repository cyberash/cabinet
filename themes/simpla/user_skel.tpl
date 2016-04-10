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

		<link rel="stylesheet" href="themes/simpla/css/style.css" type="text/css" media="screen" />

		<!-- JQuery UI Stylesheet -->
		<link type="text/css" href="themes/simpla/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" />

		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="themes/simpla/css/invalid.css" type="text/css" media="screen" />

		<!--                       Javascripts                       -->

		<!-- jQuery -->
		<script type="text/javascript" src="themes/simpla/js/jquery-1.4.2.min.js"></script>
		


		<!-- jQuery Configuration -->
		<script type="text/javascript" src="themes/simpla/js/simpla.jquery.configuration.js"></script>

		<!-- Facebox jQuery Plugin -->
		<script type="text/javascript" src="themes/simpla/js/facebox.js"></script>

		<!-- jQuery WYSIWYG Plugin -->
		<script type="text/javascript" src="themes/simpla/js/jquery.wysiwyg.js"></script>

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
<script type="text/javascript">

function lookup(inputString) {
    if(inputString.length == 0) {
        // Hide the suggestion box.
        $("#suggestions").hide();
    } else {
        $.post("ajax.php", {queryUsernames: ""+inputString+""}, function(data){
            if(data.length >0) {
                $("#suggestions").show();
                $("#autoSuggestionsList").html(data);
            }
        });
    }
} // lookup

function fill(thisValue) {
    $(‘#inputString’).val(thisValue);
   $(‘#suggestions’).hide();
}

</script>
	</head>

	<body onLoad="checkCookiep()"><div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->


		<div id="sidebar"><div id="sidebar-wrapper"> <!-- Sidebar with logo and menu -->

			<h1 id="sidebar-title"><a href="#">Multicabinet</a></h1>

			<!-- Logo (221px wide) -->
			<a href="#"><img id="logo" src="themes/simpla/images/logo.png" alt="Simpla Admin logo" /></a>

			<!-- Sidebar Profile links -->
			<div id="profile-links">
				Hello, <a href="#" title="Edit your profile">{USER.FULLNAME}</a>, you have <a href="#messages" rel="modal" title="3 Messages">3 Messages</a><br />

				<br />
				<a href="#" title="View the Site">Inbox</a> | <a href="?logout=true" title="Sign Out">Sign Out</a>
			</div>

			{FILE "themes/simpla/user_menu.tpl"}


			<div id="messages" style="display: none"> <!-- Messages are shown when a link with these attributes are clicked: href="#messages" rel="modal"  -->

				<h3>3 Messages</h3>





			</div> <!-- End #messages -->

		</div></div> <!-- End #sidebar -->

		<div id="main-content"> <!-- Main Content Section with everything -->

			<noscript> <!-- Show a notification if the user has disabled javascript -->
				<div class="notification error png_bg">
					<div>
						Javascript is disabled or is not supported by your browser. Please <a href="http://browsehappy.com/" title="Upgrade to a better browser">upgrade</a> your browser or <a href="http://www.google.com/support/bin/answer.py?answer=23852" title="Enable Javascript in your browser">enable</a> Javascript to navigate the interface properly.
					</div>
				</div>
			</noscript>

			<!-- Page Head -->
			<h2>Welcome John</h2>

			<p id="page-intro">What would you like to do?</p>
<div class="sh-buttons">
			<ul class="shortcut-buttons-set">

				<li><a class="shortcut-button" href="?object=addorder"><span>
					<img src="themes/simpla/images/pencil_48.png" alt="icon" /><br />
					{LANG.newordercustomer}
				</span></a></li>

				<li><a class="shortcut-button" href="?object=orderpkg"><span>
					<img src="themes/simpla/images/paper_content_pencil_48.png" alt="icon" /><br />
					{LANG.newticket} 
				</span></a></li>


				<li><a class="shortcut-button" href="?object=manageinvs"><span>
					<img src="themes/simpla/images/image_add_48.png" alt="icon" /><br />
					{LANG.listdueinvoicescust}
				</span></a></li>
			</ul><!-- End .shortcut-buttons-set -->
			</div>
<!-- BEGIN: stats -->
			<div class="stats png_bg">
				<div class="stats-header">
				{LANG.accountstats}
				</div>
					<div class="stats-data">
					Orders: {ORDERSCNT}<br>
					Invoices: {INVCNT}<br>
					Unpaid Invoices: {UINVCNT}<br>
					</div>
			</div>
<!-- END: stats -->
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
			
<!-- BEGIN: dashboard -->
			{FILE "themes/simpla/objects/user_dashboard.tpl"}
<!-- END: dashboard -->

<!-- BEGIN: editprofile -->
			{FILE "themes/simpla/objects/editprofile.tpl"}
<!-- END: editprofile -->

<!-- BEGIN: orderplaced -->
			{FILE "themes/simpla/objects/user_orderplaced.tpl"}
<!-- END: orderplaced -->

<!-- BEGIN: orderpkg -->
			{FILE "themes/simpla/objects/user_orderpkg.tpl"}
<!-- END: orderpkg -->

<!-- BEGIN: manageorders -->
			{FILE "themes/simpla/objects/user_manageorders.tpl"}
<!-- END: manageorders -->

<!-- BEGIN: managetickets -->
			{FILE "themes/simpla/objects/user_managetickets.tpl"}
<!-- END: managetickets -->

<!-- BEGIN: addticket -->
			{FILE "themes/simpla/objects/addticket.tpl"}
<!-- END: addticket -->

<!-- BEGIN: viewticket -->
			{FILE "themes/simpla/objects/user_viewticket.tpl"}
<!-- END: viewticket -->

<!-- BEGIN: vieworder -->
			{FILE "themes/simpla/objects/user_vieworder.tpl"}
<!-- END: vieworder -->

<!-- BEGIN: viewinvoice -->
			{FILE "themes/simpla/objects/user_viewinvoice.tpl"}
<!-- END: viewinvoice -->

<!-- BEGIN: manageinvs -->
			{FILE "themes/simpla/objects/user_manageinvs.tpl"}
<!-- END: manageinvs -->

<!-- BEGIN: personalsettings -->
			{FILE "themes/simpla/objects/user_personalsetting.tpl"}
<!-- END: personalsettings -->

			<div class="clear"></div>




			<div id="footer">
				<small> <!-- Remove this notice or replace it with whatever you want -->

						&#169; 2009 Your Company | <a href="#">{LANG.top}</a>
				</small>
			</div><!-- End #footer -->

		</div> <!-- End #main-content -->

	</div></body>


</html>
<!-- END: main -->
