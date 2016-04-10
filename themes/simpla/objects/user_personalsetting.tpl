			<div class="content-box">

				<div class="content-box-header">

					<h3>System settings</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset>
					<input type="hidden" name="action" value="updatepersonalsettings">
						<p>
							<label>Address for sending notifications:</label>
							<input class="text-input small-input" type="text" name="notifyaddress" value="{NOTIFYADDRESS}">
							
							<select name="notifymodule">
<!-- BEGIN: ntlist -->
								<option value="{NM.id}" {DEFAULT}>{NM.shortname}</option>
<!-- END: ntlist -->
							</select>
						</p>
						<p>
							<Label>Enable / Disable notification:</Label>
							<input type="checkbox" name="usernewinvoice" value="1" {NEWINVSEL} /> New invoice
							<input type="checkbox" name="userneworder" value="1" {NEWORDSEL} /> New order
							<input type="checkbox" name="usernewticket" value="1" {NEWTCSEL} /> New ticket
							<input type="checkbox" name="usernewticketreply" value="1" {NEWTRSEL} /> Ticket reply received

						</p>
						<p>
							<label>Language:</label>
							<select class="small-input" name="language">
<!-- BEGIN: langlist -->
								<option value="{LNG.code}" {DEFAULT}>{LNG.code}</option>
<!-- END: langlist -->
							</select>
						</p>
						<p>
							<label>Default Currency:</label>
							<select class="small-input" name="currency">
<!-- BEGIN: currlist -->
								<option value="{CURR.id}" {DEFAULT}>{CURR.name}</option>
<!-- END: currlist -->
							</select>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>
					
				</div>

			</div>
