<script type="text/javascript">
$(document).ready(function()
{

$("#status").change(function()
{
	alert ("Changed");return false;  
});
});
</script>
<!-- BEGIN: ticketinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.ticketidisntspecified}
							</div>
						</div>
<!-- END: ticketinfo -->
<!-- BEGIN: ticket -->

			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.ticketsharp}{TID}</h3>
					<form>
					<fieldset>
					<p>
					<label>{LANG.status}:
					<select id="status" name="status">
						<option value="Customer" {DEFCustomer}>{LANG.ticketwaitcustomer}</option>
						<option value="Support" {DEFSupport}>{LANG.ticketwaitsupport}</option>
						<option value="Closed" {DEFClosed}>{LANG.ticketclosed}</option>
						<option value="Hold" {DEFHold}>{LANG.tickethold}</option>
						<option value="Progress" {DEFProgress}>{LANG.ticketinprogress}</option>
					</select>
					</label>
					</p>
					</fieldset>
					</form>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->
				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form method="POST" action="{PHP._SERVER.PHP_SELF}">
					<fieldset>
					<input type="hidden" name="action" value="addticketreply">
					<input type="hidden" name="ticketid" value="{TID}">
					<p>
						<textarea class="text-input textarea wysiwyg" id="textarea" name="message"></textarea>
					</p>
					<p>
						<input class="button" type="submit" value="Submit" {DISABLED}/>
					</p>
					
					</fieldset>
					</form>
					</div>

				</div>
			</div>
<!-- BEGIN: ticketstatus -->
						<div class="notification information png_bg">
							<div>
								{TC.message}
							</div>
						</div>
<!-- END: ticketstatus -->
<!-- BEGIN: ticketreply -->
			<div class="ticket-box">

				<div class="ticket-box-header">

					<h3>{LANG.ticketreply} #{TC.id} {LANG.ticketreplyby} {USERNAME} {LANG.ticketreplyat} {TC.date}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="ticket-box-content">
					<div class="tab-content default-tab">
						{TC.message}
					</div>

				</div>
			</div><!-- End .ticket-box -->
<!-- END: ticketreply -->
<!-- END: ticket -->
