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
							<label>{LANG.givennames}:</label>
							<input class="text-input small-input" type="text" name="name" value="{PROFILE.name}">
							<br><small>{LANG.givennameexample}</small>
						</p>
						<p>
							<label>{LANG.surname}:</label>
							<input class="text-input small-input" type="text" name="surname" value="{PROFILE.surname}">
							<br><small>{LANG.surnameexample}</small>
						</p>
						<p>
							<label>{LANG.company}:</label>
							<input class="text-input small-input" type="text" name="company" value="{PROFILE.company}">
						</p>
						<p>
							<label>{LANG.sex}:</label>
							<select class="small-input" name="sex">
								<option value="M" {DEFAULTSM}>{LANG.sexmale}</option>
								<option value="F" {DEFAULTSF}>{LANG.female}</option>
							</select> 
						</p>
						<p>
							<label>{LANG.phonenumber}:</label>
							<input class="text-input small-input" type="text" name="phone" value="{PROFILE.phone}">
						</p>
						<p>
							<label>{LANG.country}:</label>
							<select class="small-input" name="country">
<!-- BEGIN: countrylist -->
								<option value="{ID}" {DEFAULT}>{COUNTRYNAME}</option>
<!-- END: countrylist -->
							</select>
						</p>
						<p>
							<label>{LANG.address}:</label>
							<input class="text-input small-input" type="text" name="address" value="{PROFILE.address}">
						</p>
						<p>
							<label>{LANG.city}:</label>
							<input class="text-input small-input" type="text" name="city" value="{PROFILE.city}">
						</p>
						<p>
							<label>{LANG.postcode}:</label>
							<input class="text-input small-input" type="text" name="postcode" value="{PROFILE.postcode}">
						</p>
						<p>
							<label>IM (MSN, ICQ, Jabber etc.):</label>
							<input class="text-input small-input" type="text" name="im" value="{PROFILE.im}">
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>
					
				</div>

			</div>
