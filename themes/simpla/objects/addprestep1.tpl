			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.addnewpresetstep1}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: groupslisterror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noserversgroupfound}. {LANG.please}, <a href="?object=addservergroup">{LANG.addnewgroupofservers}</a>
							</div>
						</div>
<!-- END: groupslisterror -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addprestep2">
					<fieldset>
						<p>
							<label>{LANG.serversgroupforpreset}:</label>
							<select class="small-input" name="servergroup" {DISABLED}>
<!-- BEGIN: groupslist -->
							<option value="{GROUP.id}">{GROUP.name}</option>
<!-- END: groupslist -->
							</select>
							<br><small>{LANG.choosepropergroup}</small>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
