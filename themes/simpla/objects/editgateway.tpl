			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.setuppaygateway}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: gwerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.gwnamenotspecified}
							</div>
						</div>
<!-- END: gwerror -->
<!-- BEGIN: gateway -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<div class="column-right">

						<p>
						<label>{LANG.gatewaydesc}:</label>
						{INFO.desc}
						</p>
						

					</div>
					<fieldset class="colum-left">
					<input type="hidden" name="action" value="editgateway">
					<input type="hidden" name="gwname" value="{GWNAME}">
						<p>
							<label>{LANG.gatewayname}:</label>
							{INFO.name}
						</p>
						<p>
							<label>{LANG.defcurrency}:</label>
							<select name="defcurr" class="small-input">
<!-- BEGIN: defcurr -->
							<option value="{CURR.name}" {DEFAULT}>{CURR.desc}</option>
<!-- END: defcurr -->
							</select>
						</p>
<!-- BEGIN: inputtext -->
						<p>
							<label>{INPUT.label}</label>
							<input type="text" name="{INPUT.name}" class="text-input small-input" value="{INPUT.value}">
						</p>
<!-- END: inputtext -->
					</fieldset>
					
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</form>
<!-- END: gateway -->
					</div>

				</div>

			</div>
