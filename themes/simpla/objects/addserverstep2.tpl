			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.addnewserversteptwo}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addserverstep3" />
					<input type="hidden" name="moduleid" value="{MODULEID}" />
					<input type="hidden" name="groupid" value="{GROUPID}" />
					<fieldset>
						<p>
							<label>{LANG.servername}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="servername" />
						</p>
						<p>
							<label>{LANG.maxclientsonserver}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="maxclients" />
						</p>
						<p>
							<label>{LANG.servermodule}:</label>
							<strong>{MODULENAME}</strong>
						</p>
<!-- BEGIN: inputtext -->
						<p>
							<label>{INPUT.label}</label>
							<input class="text-input small-input" type="{INPUT.type}" id="small-input" name="{INPUT.name}" />
						</p>
<!-- END: inputtext -->

						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
