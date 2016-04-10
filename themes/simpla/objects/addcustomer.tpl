			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>{LANG.createnewcustomer}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->

						<form action="{PHP._SERVER.PHP_SELF}" method="post">

							<fieldset class="column-left"> <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
								<input type="hidden" name="action" value="addcustomer">
								<p>
									<label>{LANG.username}:</label>
										<input class="text-input medium-input" type="text" id="username" name="username" /><span id="namestatus" class="input-notification"></span><!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>{LANG.pleasespecifyusername}</small>
								</p>
								<p>
									<label>{LANG.password}:</label>
										<input class="text-input medium-input" type="text" id="password" name="password" /> <span id="pwdstatus" class="input-notification"></span> <!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>{LANG.pleasespecifypassword}</small>
								</p>

								<p>
									<label>{LANG.repeatpassword}:</label>
										<input class="text-input medium-input" type="text" id="password2" name="password2" /> <span id="pwd2status" class="input-notification"></span> <!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>{LANG.pleaserepeatpassword}</small>
								</p>
								<p>
									<label>{LANG.email}:</label>
										<input class="text-input medium-input" type="text" id="email" name="email" /> <span id="emailstatus" class="input-notification"></span> <!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>{LANG.pleasespecifyemail}</small>
								</p>
								<p>
									<input class="button" type="submit" value="Submit" />
								</p>

							</fieldset>
								<fieldset class="column-right">
								<p>

									<label>{LANG.customersremarks}</label>
									<textarea class="text-input textarea wysiwyg" id="textarea" name="info" cols="79" rows="15"></textarea>
								</p>
							</fieldset>


							<div class="clear"></div><!-- End .clear -->


						</form>

					</div> <!-- End #tab1 -->


				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->
