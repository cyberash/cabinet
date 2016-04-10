				<div class="content-box-header">

					<h3>Step 3: Setup Database</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->
					<div class="tab-content default-tab" id="default">
						<form action="{PHP._SERVER.PHP_SELF}" method="post">
						<input type="hidden" name="step" value="4">
						<p>
							<label>Host:</label>
							<input class="text-input medium-input" type="text" name="host" value="localhost"/>
						</p>

						<p>
							<label>Database name:</label>
							<input class="text-input medium-input" type="text" name="dbname" value="{WWWPATH}"/>
						</p>

						<p>
							<label>Username:</label>
							<input class="text-input medium-input" type="text" name="dbuser" value="{WWWPATH}"/>
						</p>

						<p>
							<label>Password:</label>
							<input class="text-input medium-input" type="password" name="dbpassword" value="{WWWPATH}"/>
						</p>

						<p>
							<input type="submit" class="button" value="Next step"> 
						</p>
						</form>
					</div>
					
				</div> <!-- End .content-box-content -->
