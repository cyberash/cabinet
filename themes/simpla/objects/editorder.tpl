			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editorder} #{ORDERID}</h3>
					<ul class="content-box-tabs">

						<li><a href="#default" class="default-tab">{LANG.order}</a></li> <!-- href must be unique and match the id of target div -->
						<li><a href="#srvmodule">{LANG.servermodule}</a></li>
					</ul>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">
<!-- BEGIN: gwerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.orderidnotspecified}
							</div>
						</div>
<!-- END: gwerror -->
<!-- BEGIN: order -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset class="column-left">
					<input type="hidden" name="action" value="editorder">
					<input type="hidden" name="orderid" value="{ORDERID}">
						<p>
							<label>{LANG.lastinvoice}:</label>
							#<a href="?object=editinv&invid={LASTINV.id}">{LASTINV.id}</a>, {LASTINV.status}
						</p>
						<p>
							<label>{LANG.customer}:</label>
							<a href="?object=managecustomer&username={USER.username}">{USER.username}</a>, {USERSTATUS}
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
					
					<fieldset class="colum-right">

						<p>
						<label>{LANG.package}:</label>
						<select name="defpkg" class="small-input">
<!-- BEGIN: inputpkg -->
							<option value="{PKG.id}" {DEFAULT}>{PKG.name}</option>
<!-- END: inputpkg -->
						</select>
						</p>

						<p>
							<label>{LANG.status}:</label>
						<select name="orderstatus" class="small-input">
							<option value="Active" {StatusActive}>{LANG.statusactive}</option>
							<option value="Pending" {StatusPending}>{LANG.statuspending}</option>
							<option value="Suspended" {StatusSuspended}>{LANG.statussuspended}</option>
							<option value="Terminated" {StatusTerminated}>{LANG.statusterminated}</option>
						</select>
						</p>

						<p>
							<label>{LANG.paycycle}:</label>
							<select name="ordercycle" class="small-input">
							<option value="1" {DEF1}>{LANG.monthlycycle}</option>
							<option value="3" {DEF3}>{LANG.quarterlycycle}</option>
							<option value="6" {DEF6}>{LANG.semiannuallycycle}</option>
							<option value="12" {DEF12}>{LANG.annuallycycle}</option>
							<option value="24" {DEF24}>{LANG.biennially}</option>
							</select>
						</p>

						<p>
							<label>{LANG.dateordered}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="orderdate" value="{ORDERDATE}" />
						</p>

						<p>
							<label>{LANG.nextdue}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="nextdue" value="{NEXTDUE}" />
						</p>

						<p>
							<label>{LANG.firstamount}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="firstamount" value="{FIRSTAMOUNT}" />
						</p>

						<p>
							<label>{LANG.recuramount}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="recuramount" value="{RECURAMOUNT}" />
						</p>

						<p>
							<label>{LANG.recalculateamounts}:</label>
							<input name="checkbox1" type="checkbox"> {LANG.checktorecalculate}
						</p>
					
					</fieldset>

					</form>
<!-- END: order -->
					</div>
					<div class="tab-content" id="srvmodule">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="updateorderserverdata">
					<input type="hidden" name="id" value="{ORDERID}">
					<fieldset class="column-left">
					<p>
						<label>{LANG.server}:</label>
						<select name="serverid" class="small-input">
						<option>No server selected</option>
<!-- BEGIN: server -->
						<option value="{SRV.ServerID}" {DEFAULT}>{SRV.servername}</option>
<!-- END: server -->
						</select>
					</p>
					<p>
						<input class="button" type="submit" value="Submit" {DISABLED}/>
					</p>
					</fieldset>
					<fieldset>
					<p>
						<label>{LANG.modulecommand}:</label>
						<a href="?action=moduleaction&do=Create&orderid={ORDERID}"><button class="button" type="button">{LANG.modulecreate}</button></a>&nbsp;
						<button class="button" type="button">{LANG.modulesuspend}</button>&nbsp;
						<button class="button" type="button">{LANG.moduleunsuspend}</button>&nbsp;
						<a href="?action=moduleaction&do=Terminate&orderid={ORDERID}"><button class="button" type="button">{LANG.moduleterminate}</button></a>&nbsp;
						<button class="button" type="button">{LANG.moduleupdate}</button>
					</p>
					<p>
						<input name="checkbox1" type="checkbox"> {LANG.preventautotermination}
					</p>
					</fieldset>
					</form>
					</div>
				</div>

			</div>
