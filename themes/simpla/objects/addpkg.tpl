			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.addnewpackage}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: preseterror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nopresets}. {LANG.please}, <a href="?object=addprestep1">{LANG.addnewpreset}</a>
							</div>
						</div>
<!-- END: preseterror -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addpkg">
					<fieldset>
						<p>
							<label>{LANG.packagename}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="name" />
						</p>
						<p>
							<label>{LANG.preset}:</label>
							<select class="small-input" name="presetid">
<!-- BEGIN: preslist -->
							<option value="{PRESET.id}">{PRESET.name}</option>
<!-- END: preslist -->
							</select>
							<br><small>{LANG.chooseproperpreset}</small>
						</p>
						<p>
							<label>{LANG.baseprice}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="price" />
							<br><small>{LANG.basemonthlyprice}</small>
						</p>
						<p>
							<label>{LANG.paymenttype}:</label>
							<select class="small-input" name="paytype">
							<option value="Recurring">{LANG.paytyperecurring}</option>
							<option value="Free">{LANG.paytypefree}</option>
							<option value="Onetime">{LANG.paytypeonetime}</option>
							</select>
						</p>
						<p>
							<label>{LANG.stock}</label>
							<input class="text-input small-input" type="text" id="small-input" name="stock" />
							<br><small>{LANG.ifyouhaveunlimiteditems}</small>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
