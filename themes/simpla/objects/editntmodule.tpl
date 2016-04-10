			<div class="content-box">

				<div class="content-box-header">

					<h3>Setup notification module</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: notfound -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								Module not found
							</div>
						</div>
<!-- END: notfound -->
<!-- BEGIN: table -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<div class="column-right">

						<p>
						<label>Module description:</label>
						{INFO.desc}
						</p>
						

					</div>
					
					<fieldset>
					<input type="hidden" name="action" value="updatentmodule">
					<input type="hidden" name="moduleid" value="{MODULEID}">
						<p>
							<label>{LANG.modulename}:</label>
							{INFO.name}
						</p>
<!-- BEGIN: inputtext -->
						<p>
							<label>{INPUT.label}:</label>
							<input type="{INPUT.type}" name="{INPUT.name}" value="{VALUE}">
						</p>
<!-- END: inputtext -->
					</fieldset>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</form>
<!-- END: table -->
					</div>

				</div>

			</div>
