			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>{LANG.generalsettings}</h3>

					<ul class="content-box-tabs">

						<li><a href="#tab1" class="default-tab">{LANG.system}</a></li> <!-- href must be unique and match the id of target div -->
						<li><a href="#currency">{LANG.paymentgatewayscurrency}</a></li>
						<li><a href="#notifications">{LANG.notifications}</a></li>
						<li><a href="#localization">{LANG.localisation}</a></li>
						<li><a href="#security">{LANG.security}</a></li>
					</ul>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->

<form action="" method="post">
	<fieldset class="column-left">
		<p>
			<label>{LANG.companyname}:</label>
			<input class="text-input medium-input" type="text" id="medium-input" name="small-input" />
		</p>
		<p>
			<label>{LANG.systemurl}:</label>
			<input class="text-input medium-input" type="text" id="medium-input" name="small-input" />
			<br /><small>{LANG.withoutwwwhttps}</small>
		</p>
		<p>
			<label>[LANG.designtemplate]:</label>
			<input class="text-input medium-input" type="text" id="medium-input" name="small-input" />
		</p>
		<p>
			<label>[LANG.logourl]:</label>
			<input class="text-input medium-input" type="text" id="medium-input" name="small-input" />
		</p>

	</fieldset>
	<fieldset class="colum-right">
		<p>
			<label>{LANG.enablemaintenance}</label>
			<input type="checkbox" name="checkbox1" /> {LANG.yesmaintenance}
		</p>
		<p>
			<label>{LANG.maintenancemessage}:</label>
			<textarea class="text-input textarea wysiwyg" id="textarea" name="textfield" cols="79" rows="15"></textarea>
		</p>
	</fieldset>		<p>
									<input class="button" type="submit" value="Submit" />
		</p>
</form>

					</div> <!-- End #tab1 -->


					<div class="tab-content" id="currency"> <!-- Start #currency -->
					<form action="{PHP._SERVER.PHP_SELF}" method="post">
					
<!-- BEGIN: currerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nocurrencies}. 
							</div>
						</div>
<!-- END: currerror -->

						<fieldset>
							<input type="hidden" name="action" value="currencysetupdate">
							<p>
								<label>{LANG.systemdefcurrency}</label>
								<select name="defcurrency" class="small-input">
<!-- BEGIN: curlist -->
								<option value="{CURR.name}" {DEFAULT}>{CURR.desc} ({CURR.name})</option>
<!-- END: curlist -->
								</select>
							</p>
							<p>
								<label>{LANG.currencysource}</label>
								<select name="defcurrencysource" class="small-input">
									<option value="no">{LANG.disableautoupdate}</option>
<!-- BEGIN: sourcelist -->
									<option value="{PROVIDER.name}" {DEFAULT}>{PROVIDER.info.name}</option>
<!-- END: sourcelist -->
								</select>
								<br><small><a href="">{LANG.clicktoupdaterates}</a></small>
							</p>
							<p>
								<label>{LANG.defcurrencysymbol}</label>
								<input class="text-input small-input" type="text" id="small-input" name="currencysymbol" value="{SYMBOL}" />
								<br><small>{LANG.egbuckseuro}</small>
							</p>
							<p>
								<label>{LANG.defaulpaymodule}</label>
								<select name="defpaymodule" class="small-input">
<!-- BEGIN: defaultpm -->
								<option value="{PM.id}" {DEFAULT}>{PM.modulename}</option>
<!-- END: defaultpm -->
								</select>
							</p>
							<p>
									<input class="button" type="submit" value="Submit" {DISABLED}/>
							</p>
						</fieldset>
					</form>
					</div><!-- End #currency -->


					<div class="tab-content" id="localization">

						<form action="{PHP._SERVER.PHP_SELF}" method="post">

							<fieldset> <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
							<input type="hidden" name="action" value="updatelang">
								<p>
									<label>{LANG.defsystemlang}:</label>
									<select name="langcode" class="small-input">
<!-- BEGIN: langlist -->
										<option value="{LANGCODE}">{LANGCODE}</option>
<!-- END: langlist -->
									</select><br />
									<a href="?action=updatelangs"><small>{LANG.updatelanglist}</small></a>
								</p>

								<p>
									<input class="button" type="submit" value="Submit" />
								</p>

							</fieldset>

							<div class="clear"></div><!-- End .clear -->


						</form>

					</div> <!-- End #localization -->

						<div class="tab-content" id="notifications">

						<form action="{PHP._SERVER.PHP_SELF}" method="post">

							<fieldset>
								<input type="hidden" name="action" value="updatedefnotifymodule">
								<p>
									<label>Default Notification Module:</label>
									<select name="nmoduleid" class="small-input">
<!-- BEGIN: modulelist -->
									<option value="{NMODULE.id}" {DEFAULT}>{NMODULE.name}</option>
<!-- END: modulelist -->
									</select>
								</p>

								<p>
									<input class="button" type="submit" value="Submit" />
								</p>

							</fieldset>

							<div class="clear"></div><!-- End .clear -->


						</form>

						</div> <!-- End #notifications -->
				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->
