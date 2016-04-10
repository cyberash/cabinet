<!-- BEGIN: main --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Multicabinet | {TITLE}</title>

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
function instlookup(inputString) {
    if(inputString.length == 0) {
        // Hide the suggestion box.
        $("#receivedsuggestions").hide();
    } else {
        $.get("ajax.php", {querySearch: ""+inputString+""}, function(data){
            if(data.length >0) {
                $("#receivedsuggestions").show();
                $("#receivedsuggestions").html(data);
            }
        });
    }
} // lookup
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
    $("#inputString").val(thisValue);
   $("#suggestions").hide();
}

</script>
	</head>

	<body><div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->
		<div id="searchbar">
			<div class="content-box-header">
			<form name="searchform">
				<input class="search-input" type="text" name="instantsuggestions"  onkeyup="instlookup(this.value);" value=""><a href="javascript:document.searchform.reset()"><img src="themes/simpla/images/cross.png" alt="Reset"></a>
			</form>

			</div>

		</div>
		<div id="receivedsuggestions"></div>

		<div id="sidebar"><div id="sidebar-wrapper"> <!-- Sidebar with logo and menu -->

			<h1 id="sidebar-title"><a href="#">Multicabinet</a></h1>

			<!-- Logo (221px wide) -->
			<a href="#"><img id="logo" src="themes/simpla/images/logo.png" alt="Multicabinet logo" /></a>

			<!-- Sidebar Profile links -->
			<div id="profile-links">
				<a href="#" title="View the Site">Inbox</a> | <a href="?logout=true" title="Sign Out">Sign Out</a>
			</div>

			{FILE "themes/simpla/admin_menu.tpl"}

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
			<h2>{TITLE}</h2>

			<p id="page-intro">{DESCR}</p>

			<ul class="shortcut-buttons-set">

				<li><a class="shortcut-button" href="#"><span>
					<img src="themes/simpla/images/pencil_48.png" alt="icon" /><br />
					{LANG.createnewcustomer}
				</span></a></li>

				<li><a class="shortcut-button" href="#"><span>
					<img src="themes/simpla/images/paper_content_pencil_48.png" alt="icon" /><br />
					{LANG.createneworder} 
				</span></a></li>


				<li><a class="shortcut-button" href="#"><span>
					<img src="themes/simpla/images/image_add_48.png" alt="icon" /><br />
					{LANG.sendinvoice}
				</span></a></li>

				<li><a class="shortcut-button" href="#"><span>
					<img src="themes/simpla/images/clock_48.png" alt="icon" /><br />
					Generate Due Invoices
				</span></a></li>

				<li><a class="shortcut-button" href="#messages" rel="modal"><span>
					<img src="themes/simpla/images/comment_48.png" alt="icon" /><br />
					Mass Mail
				</span></a></li>

			</ul><!-- End .shortcut-buttons-set -->

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
<!-- BEGIN: addcustomer -->
			{FILE "themes/simpla/objects/addcustomer.tpl"}
<!-- END: addcustomer -->

<!-- BEGIN: dashboard -->
			{FILE "themes/simpla/objects/admin_dashboard.tpl"}
<!-- END: dashboard -->

<!-- BEGIN: managecustomers -->
			{FILE "themes/simpla/objects/managecustomers.tpl"}
<!-- END: managecustomers -->

<!-- BEGIN: manageservermodules -->
			{FILE "themes/simpla/objects/manageservermodules.tpl"}
<!-- END: manageservermodules -->

<!-- BEGIN: manageservergroups -->
			{FILE "themes/simpla/objects/manageservergroups.tpl"}
<!-- END: manageservergroups -->

<!-- BEGIN: addservergroup -->
			{FILE "themes/simpla/objects/addservergroup.tpl"}
<!-- END: addservergroup -->

<!-- BEGIN: addserverstep1 -->
			{FILE "themes/simpla/objects/addserverstep1.tpl"}
<!-- END: addserverstep1 -->

<!-- BEGIN: addserverstep2 -->
			{FILE "themes/simpla/objects/addserverstep2.tpl"}
<!-- END: addserverstep2 -->

<!-- BEGIN: manageservers -->
			{FILE "themes/simpla/objects/manageservers.tpl"}
<!-- END: manageservers -->

<!-- BEGIN: managepresgroups -->
			{FILE "themes/simpla/objects/managepresgroups.tpl"}
<!-- END: managepresgroups -->

<!-- BEGIN: addprestep1 -->
			{FILE "themes/simpla/objects/addprestep1.tpl"}
<!-- END: addprestep1 -->

<!-- BEGIN: addprestep2 -->
			{FILE "themes/simpla/objects/addprestep2.tpl"}
<!-- END: addprestep2 -->

<!-- BEGIN: managepkgs -->
			{FILE "themes/simpla/objects/managepkgs.tpl"}
<!-- END: managepkgs -->

<!-- BEGIN: addpkg -->
			{FILE "themes/simpla/objects/addpkg.tpl"}
<!-- END: addpkg -->

<!-- BEGIN: addorder -->
			{FILE "themes/simpla/objects/addorder.tpl"}
<!-- END: addorder -->

<!-- BEGIN: manageorders -->
			{FILE "themes/simpla/objects/manageorders.tpl"}
<!-- END: manageorders -->

<!-- BEGIN: addinv -->
			{FILE "themes/simpla/objects/addinv.tpl"}
<!-- END: addinv -->

<!-- BEGIN: managegatewaymodules -->
			{FILE "themes/simpla/objects/managegatewaymodules.tpl"}
<!-- END: managegatewaymodules -->

<!-- BEGIN: manageinvs -->
			{FILE "themes/simpla/objects/manageinvs.tpl"}
<!-- END: manageinvs -->

<!-- BEGIN: generalsettings -->
			{FILE "themes/simpla/objects/generalsettings.tpl"}
<!-- END: generalsettings -->

<!-- BEGIN: editgateway -->
			{FILE "themes/simpla/objects/editgateway.tpl"}
<!-- END: editgateway -->

<!-- BEGIN: editorder -->
			{FILE "themes/simpla/objects/editorder.tpl"}
<!-- END: editorder -->

<!-- BEGIN: editinv -->
			{FILE "themes/simpla/objects/editinv.tpl"}
<!-- END: editinv -->

<!-- BEGIN: managetrans -->
			{FILE "themes/simpla/objects/managetrans.tpl"}
<!-- END: managetrans -->

<!-- BEGIN: editserver -->
			{FILE "themes/simpla/objects/editserver.tpl"}
<!-- END: editserver -->

<!-- BEGIN: geninvoices -->
			{FILE "themes/simpla/objects/geninvoices.tpl"}
<!-- END: geninvoices -->

<!-- BEGIN: editcron -->
			{FILE "themes/simpla/objects/editcron.tpl"}
<!-- END: editcron -->

<!-- BEGIN: editpkg -->
			{FILE "themes/simpla/objects/editpkg.tpl"}
<!-- END: editpkg -->

<!-- BEGIN: managedepartments -->
			{FILE "themes/simpla/objects/managedepartments.tpl"}
<!-- END: managedepartments -->

<!-- BEGIN: adddep -->
			{FILE "themes/simpla/objects/adddep.tpl"}
<!-- END: adddep -->

<!-- BEGIN: editdep -->
			{FILE "themes/simpla/objects/editdep.tpl"}
<!-- END: editdep -->

<!-- BEGIN: managecustomer -->
			{FILE "themes/simpla/objects/managecustomer.tpl"}
<!-- END: managecustomer -->

<!-- BEGIN: managetickets -->
			{FILE "themes/simpla/objects/managetickets.tpl"}
<!-- END: managetickets -->

<!-- BEGIN: managenotifies -->
			{FILE "themes/simpla/objects/managenotifies.tpl"}
<!-- END: managenotifies -->

<!-- BEGIN: viewticket -->
			{FILE "themes/simpla/objects/viewticket.tpl"}
<!-- END: viewticket -->

<!-- BEGIN: managecurrs -->
			{FILE "themes/simpla/objects/managecurrs.tpl"}
<!-- END: managecurrs -->

<!-- BEGIN: editcurr -->
			{FILE "themes/simpla/objects/editcurr.tpl"}
<!-- END: editcurr -->

<!-- BEGIN: managenotifytemplates -->
			{FILE "themes/simpla/objects/managenotifytemplates.tpl"}
<!-- END: managenotifytemplates -->

<!-- BEGIN: addcurr -->
			{FILE "themes/simpla/objects/addcurr.tpl"}
<!-- END: addcurr -->

<!-- BEGIN: addnotifytemplate -->
			{FILE "themes/simpla/objects/addnotifytemplate.tpl"}
<!-- END: addnotifytemplate -->

<!-- BEGIN: editnotifytemplate -->
			{FILE "themes/simpla/objects/editnotifytemplate.tpl"}
<!-- END: editnotifytemplate -->

<!-- BEGIN: managenotifymodules -->
			{FILE "themes/simpla/objects/managenotifymodules.tpl"}
<!-- END: managenotifymodules -->

<!-- BEGIN: editntmodule -->
			{FILE "themes/simpla/objects/editntmodule.tpl"}
<!-- END: editntmodule -->

<!-- BEGIN: personalsettings -->
			{FILE "themes/simpla/objects/personalsetting.tpl"}
<!-- END: personalsettings -->

<!-- BEGIN: sendmessage -->
			{FILE "themes/simpla/objects/sendmessage.tpl"}
<!-- END: sendmessage -->

<!-- BEGIN: editcustomer -->
			{FILE "themes/simpla/objects/editcustomer.tpl"}
<!-- END: editcustomer -->

<!-- BEGIN: editpreset -->
			{FILE "themes/simpla/objects/editpreset.tpl"}
<!-- END: editpreset -->

<!-- BEGIN: editservergroup -->
			{FILE "themes/simpla/objects/editservergroup.tpl"}
<!-- END: editservergroup -->

			<div class="clear"></div>




			<div id="footer">
				<small> <!-- Remove this notice or replace it with whatever you want -->

						&#169; 2009 Your Company | <a href="#">Top</a>
				</small>
			</div><!-- End #footer -->

		</div> <!-- End #main-content -->

	</div></body>


</html>
<!-- END: main -->
