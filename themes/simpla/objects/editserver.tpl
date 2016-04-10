			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editserver} #{SRV.ServerID}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">
<!-- BEGIN: gwerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.serveridisntspecified}
							</div>
						</div>
<!-- END: gwerror -->
<!-- BEGIN: server -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset>
					<input type="hidden" name="action" value="editserver">
					<input type="hidden" name="serverid" value="{SRV.ServerID}">
						<p>
							<label>{LANG.servername}:</label>
							<input class="text-input small-input" type="text" name="servername" value="{SRV.servername}">
						</p>
						<p>
							<label>{LANG.status}:</label>
							<select name="serverstatus">
								<option value="1" {Status1}>{LANG.statusenabled}</option>
								<option value="0" {Status0}>{LANG.statusdisabled}</option>
							</select>
						</p>
							<label>{LANG.maxclientsonserver}:</label>
							<input class="text-input small-input" type="text" name="maxclients" value="{SRV.maxclients}"> {LANG.currentlyaccountsonserver}: {CURRACC}
						</p>
<!-- BEGIN: inputtext -->
						<p>
							<label>{INPUT.label}:</label>
							<input class="text-input small-input" type="text" name="{INPUT.name}" value="{INPUT.value}">
						</p>
<!-- END: inputtext -->

						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
<!-- END: server -->
					</div>
					
				</div>

			</div>
