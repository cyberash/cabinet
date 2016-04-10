			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.createneworder}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: usererror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noactiveusers}.</a>
							</div>
						</div>
<!-- END: usererror -->
<!-- BEGIN: pkgerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nopresets}.</a>
							</div>
						</div>
<!-- END: pkgerror -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addorder">
					<fieldset>
						<p>
							<label>{LANG.customer}:</label>
							<select class="small-input" name="customerid">
<!-- BEGIN: userlist -->
							<option value="{USER.id}">{USER.username}</option>
<!-- END: userlist -->
							</select>
						</p>
						<p>
							<label>{LANG.product}:</label>
							<select class="small-input" name="pkgid">
<!-- BEGIN: pkglist -->
							<option value="{PKG.PackageID}">{PKG.name}</option>
<!-- END: pkglist -->
							</select>
							<br><small>{LANG.chooseproducttoorder}.</small>
						</p>
						<p>
							<label>{LANG.paycycle}:</label>
							<select class="small-input" name="cycle">
							<option value="1">{LANG.monthlycycle}</option>
							<option value="3">{LANG.quarterlycycle}</option>
							<option value="6">{LANG.semiannuallycycle}</option>
							<option value="12">{LANG.annuallycycle}</option>
							<option value="24">{LANG.biennially}</option>
							<option value="-1">{LANG.nocycle}</option>
							</select>
						</p>
						<p>
							<label>{LANG.sendinvoice}</label>
							<input type="checkbox" name="createinvoice" value="1" checked>
							<br><small>{LANG.ifyoudontwantthisorder}</small>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
