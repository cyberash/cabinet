			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editpackage}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="editpkg">
					<input type="hidden" name="pkgid" value="{PKG.id}">
					<fieldset class="column-right">
					<label>{LANG.description}:</label>
<textarea style="display: none;" id="textarea"" class="text-input textarea wysiwyg" name="desc" cols="40" rows="15">{PKG.desc}</textarea>
					<br><small>{LANG.fulldescforpkg}</small>
					<label>Order link:</label>
					<input class="text-input medium-input" type="text" value="http://{PHP._SERVER.HTTP_HOST}/order.php?id={PKG.id}">
					</fieldset>
					<fieldset>
						<p>
							<label>{LANG.packagename}:</label>
							<input class="text-input small-input" type="text" name="name" value="{PKG.name}">
							<br><small>{LANG.examplehostingpkg}</small>
						</p>
						<p>
							<label>{LANG.preset}:</label>
							<select name="presetid" class="small-input">

<!-- BEGIN: preset -->
								<option value="{PRESET.id}" {DEFAULT}>{PRESET.name}</option>
<!-- END: preset -->
							</select>
						</p>
						<p>
							<label>{LANG.paymenttype}:</label>
							<select name="paytype" class="small-input">
							<option value="Free" {DEFFree}>{LANG.paytypefree}</option>
							<option value="Onetime" {DEFOnetime}>{LANG.paytypeonetime}</option>
							<option value="Recurring" {DEFRecurring}>{LANG.paytyperecurring}</option>
							</select>
						</p>
						<p>
							<label>{LANG.stock}:</label>
							<input class="text-input small-input" type="text" name="stock" value="{PKG.stock}">
						</p>
						<p>
							<label>{LANG.baseprice}:</label>
							<input class="text-input small-input" type="text" name="price" value="{PKG.price}">
							<br><small>{LANG.basemonthlyprice}</small>
						</p>

						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>

					</form>
					</div>
					
				</div>

			</div>
