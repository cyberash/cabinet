	<script type="text/javascript">

	$(function() {
	
		$("#datecreated").datepicker({ dateFormat: 'yy-mm-dd 00:00:00' });
		$("#datedue").datepicker({ dateFormat: 'yy-mm-dd 00:00:00' });
	});
	
	</script>
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.sendinvoice}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: custerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noactiveusers}
							</div>
						</div>
<!-- END: custerror -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addinv">
					<fieldset>
						<p>
							<label>{LANG.customerusername}:</label>
							<input class="text-input small-input" type="text" id="small-input queryUsernames" name="custname" />
						</p>
						<p>
							<label>{LANG.ordersharp}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="orderid" />
						</p>
						<p>
							<label>{LANG.amount}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="amount" />
						</p>
						<p>
							<label>{LANG.datecreated}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="datecreated" value="{TODAY}" />
						</p>
						<p>
							<label>{LANG.duedate}:</label>
							<input class="text-input small-input" type="text" id="datedue" name="datedue" value="{TODAY}" />
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
