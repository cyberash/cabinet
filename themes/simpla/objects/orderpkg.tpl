			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.newordercustomer}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->

					<div class="tab-content default-tab" id="default">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="object" value="orderpkg">
					<input type="hidden" name="pkgid" value="{PKG.PackageID}">
					<fieldset >
						<label>{LANG.selectpkgtopurchase}:</label>
						<select name="pkgid" class="small-input" onchange="this.form.submit();">
<!-- BEGIN: pkgrow -->
						<option value="{PKGR.id}" {DEFAULT}>{PKGR.name}</option>
<!-- END: pkgrow -->
						</select>
					</fieldset>
					</form>
					</div>
					
				</div> <!-- End .content-box-content -->

			</div><!-- End .content-box -->
<!-- BEGIN: pkgorder -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.order} {PKG.name}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->
					<div class="tab-content default-tab" id="default">
					<form action="reg.php" method="POST">
					<input type="hidden" name="action" value="placeorder">
					<input type="hidden" name="pkgid" value="{PKG.id}">
					<fieldset>
<!-- BEGIN: desc -->
						<div class="notification success png_bg" width="50%">
						{PKG.desc}
						</div>
<!-- END: desc -->
						<p><label>{LANG.monthlyprice}: {PRICE}</label></p>
						<p>
							<label>{LANG.paycycle}:</label>
							<select name="cycle" {CYCLEDISABLED}>
								<option value="1">{LANG.monthlycycle}</option>
								<option value="3">{LANG.quarterlycycle}</option>
								<option value="6">{LANG.semiannuallycycle}</option>
								<option value="12">{LANG.annuallycycle}</option>
								<option value="24">{LANG.biennially}</option>
							</select>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>
					
				</div> <!-- End .content-box-content -->

			</div>
<!-- END: pkgorder -->
<!-- BEGIN: pkginfo -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>Not found</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->
					<div class="tab-content default-tab" id="default">
						Package not found with specified id: {ID}
					</div>
					
				</div> <!-- End .content-box-content -->

			</div>
<!-- END: pkginfo -->