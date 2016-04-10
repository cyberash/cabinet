<!-- BEGIN: info -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No server group id is specified or id in wrong format
							</div>
						</div>
<!-- END: info -->
<!-- BEGIN: table -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>Edit group of servers #{GROUP.id}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="groupid" value="{GROUP.id}">
					<input type="hidden" name="action" value="updateservergroup">
					<fieldset>
						<p>
							<label>{LANG.groupname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="name" value="{GROUP.name}" />
							<br><small>{LANG.choosenamefornewgroup}</small>
						</p>
						<p>
							<label>{LANG.servermodule}:</label>
							<select class="small-input" name="moduleid" {DISABLED}>
<!-- BEGIN: moduleslist -->
							<option value="{MODULE.id}" {DEFAULT} >{MODULE.modulename}</option>
<!-- END: moduleslist -->
							</select>
<!-- BEGIN: moduleserror -->
							<span class="input-notification error png_bg">{LANG.nousablemodulesfound}</span>
<!-- END: moduleserror -->
							<br />
							<small>{LANG.selectpropermodule}</small>
						</p>
						<p>
							<label>Group status:</label>
							<select class="small-input" name="status">
								<option value="1" {DEF1}>Enabled</option>
								<option value="0" {DEF0}>Disabled</option>
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
<!-- END: table -->
