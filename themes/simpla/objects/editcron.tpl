			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.cronsettings}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">
<!-- BEGIN: cron -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset>
					<input type="hidden" name="action" value="editcron">
						<p>
							<label>{LANG.internalcronjob}:</label>
							<input class="text-input small-input" type="text" value="{INTCRON}" disabled="disabled">
							<br><small>Set the this cron job command to be executed daily</small>
						</p>
						<p>
							<label>{LANG.externalcronjob}:</label>
							<input class="text-input small-input" type="text" value="{EXTCRON}" disabled="disabled">
							<br><small>{LANG.setupthiscronjob}</small>
						</p>
							<label>{LANG.allowautomaticservermoduleaction}:</label>
							<input name="autosusp" type="checkbox" {AUTOSUSP1}> {LANG.autosuspension} <input name="autoterm" type="checkbox" {AUTOTERM1}> {LANG.autotermination}
						</p>
						<p>
							<label>{LANG.invgenerationdays}</label>
							<input class="text-input small-input" type="text" name="daystonewinv" value="{DAYSTONEWINV}">
							<br><small>{LANG.numberofdaysbeforeordersdueday}</small>
						</p>
						<p>
							<label>{LANG.ordersuspdays}</label>
							<input class="text-input small-input" type="text" name="daystosuspend" value="{SUSPDAYS}">
							<br><small>{LANG.numberofdaystowaitbeforesuspend}</small>
						</p>
						<p>
							<label>{LANG.orderterminationdays}</label>
							<input class="text-input small-input" type="text" name="daystoterminate" value="{TERMDAYS}">
							<br><small>{LANG.numberofdaysbeforesuspendit}</small>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
<!-- END: cron -->
					</div>
					
				</div>

			</div>
