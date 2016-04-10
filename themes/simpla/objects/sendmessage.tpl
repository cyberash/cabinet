<!-- BEGIN: mesinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No notification modules found
							</div>
						</div>
<!-- END: mesinfo -->
<!-- BEGIN: message -->

			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.sendmessage}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->
				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form method="POST" action="{PHP._SERVER.PHP_SELF}">
					<fieldset>
					<input type="hidden" name="action" value="sendmessage">
					<p>
						<label>Select customer to send massage:</label>
						<select name="sendtousers">
							<option value="all">All</option>
<!-- BEGIN: customers -->
							<option value="{CUST.id}">{CUST.username} - {CUST.email}</option>
<!-- END: customers -->
							
						</select>
					</p>
					<p>
						<label>Subject:</label>
						<input class="text-input small-input" type="text" name="subject" value="">
					</p>
					<p>
						<label>Message:</label>
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
<!-- END: message -->
