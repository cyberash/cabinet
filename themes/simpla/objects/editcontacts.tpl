			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.contactprofile}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset>
					<input type="hidden" name="action" value="editprofile">
						<p>
							<label>Address for sending notifications:</label>
							<input class="text-input small-input" type="text" name="phone" value="{PROFILE.phone}">
							<select>
								<option></option>
							</select>
						</p>
						<p>
							<label>Language:</label>
							<input class="text-input small-input" type="text" name="phone" value="{PROFILE.phone}">
						</p>
						<p>
							<label>Default Currency:</label>
							<input class="text-input small-input" type="text" name="phone" value="{PROFILE.phone}">
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>
					
				</div>

			</div>
