			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>{LANG.edituser}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->

						<form action="{PHP._SERVER.PHP_SELF}" method="post">

							<fieldset class="column-left"> <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
								<input type="hidden" name="action" value="updatecustomer">
								<input type="hidden" name="userid" value="{CUST.id}">
								<p>
									<label>{LANG.username}:</label>
										<input class="text-input medium-input" type="text" id="username" name="username" value="{CUST.username}" /><span id="namestatus" class="input-notification"></span><!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>{LANG.pleasespecifyusername}</small>
								</p>
								<p>
									<label>{LANG.password}:</label>
										<input class="text-input medium-input" type="text" id="password" name="password" /> <span id="pwdstatus" class="input-notification"></span> <!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>Specify new password</small>
								</p>
								<p>
									<label>{LANG.email}:</label>
										<input class="text-input medium-input" type="text" id="email" name="email" value="{CUST.email}" /> <span id="emailstatus" class="input-notification"></span> <!-- Classes for input-notification: success, error, information, attention -->

										<br /><small>{LANG.pleasespecifyemail}</small>
								</p>
								<p>
									<label>Status:</label>
										<select class="small-input" name="presetid">
											<option value="Active" {DEFActive}>Active</option>
											<option value="Admin" {DEFAdmin}>Admin</option>
											<option value="Suspend" {DEFSuspend}>Suspended</option>
										</select>
								</p>
								<p>
									<input class="button" type="submit" value="Submit" />
								</p>

							</fieldset>
							<fieldset class="column-right">
								<p>

									<label>{LANG.customersremarks}</label>
									<textarea class="text-input textarea wysiwyg" id="textarea" name="info" cols="79" rows="15">{CUST.info}</textarea>
								</p>
							</fieldset>


							<div class="clear"></div><!-- End .clear -->


						</form>

					</div> <!-- End #tab1 -->


				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->
