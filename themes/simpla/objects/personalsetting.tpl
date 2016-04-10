			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.settings}</h3>
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
							<input type="checkbox" name="adminnewuser" value="1" {NEWUSERSEL}/> New user registered
							<input type="checkbox" name="adminneworder" value="1" {NEWORDERSEL}/> New order placed
							<input type="checkbox" name="adminnewticket" value="1" {NEWTCSEL}/> New ticket submitted
							<input type="checkbox" name="adminnewticketreply" value="1" {NEWTRSEL}/> Ticket reply received
							<input type="checkbox" name="dailyreport" value="1" {DAILYREPORT}/> Daily billing report

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
								<option value="{CURR.name}" {DEFAULT}>{CURR.name}</option>
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
