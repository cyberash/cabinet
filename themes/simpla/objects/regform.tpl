
				<div class="content-box-header">

					<h3>Registration</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->
					<div class="tab-content default-tab" id="default">
						Are you already registered? <a href="/">Then login there >></a>
					<form method="POST" action="{PHP._SERVER.PHP_SELF}">
					<input type="hidden" name="regDo" value="1">
					<fieldset>
					
					<p>
						<label>Username:</label>
						<input class="text-input small-input" type="text" name="username">
						<br /><small>Please, use only english letters and numbers</small>
					</p>
					<p>
						<label>E-mail:</label>
						<input class="text-input small-input" type="text" name="email">
						<br /><small>Ex: mail@domain.com</small>
					</p>
					<p>
						<label>Password:</label>
						<input class="text-input small-input" type="password" name="password">
						<br /><small>6-20 symbols </small>
					</p>
					<p>
						<label>Confirm password:</label>
						<input class="text-input small-input" type="password" name="password2">
					</p>
					<p>
						<input class="button" type="submit" value="Submit" {DISABLED}/>
					</p>
					
					</fieldset>
					</form>
					</div>
					
				</div> <!-- End .content-box-content -->