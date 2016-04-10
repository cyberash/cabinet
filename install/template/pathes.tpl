				<div class="content-box-header">

					<h3>Step 2: Confirm Pathes</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->
					<div class="tab-content default-tab" id="default">
						<form action="{PHP._SERVER.PHP_SELF}" method="post">
						<input type="hidden" name="step" value="3">
						<p>
							<label>System domain:</label>
							<input class="text-input medium-input" type="text" id="domain" name="domain" value="{DOMAIN}"/>
						</p>

						<p>
							<label>WWW patch:</label>
							<input class="text-input medium-input" type="text" id="wwwpath" name="wwwpath" value="{WWWPATH}"/>
						</p>

						<p>
							<input type="submit" class="button" value="Next step"> 
						</p>
						</form>
					</div>
					
				</div> <!-- End .content-box-content -->
