			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.cretenewdep}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="adddep">
					<fieldset>
						<p>
							<label>{LANG.departmentname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="name" />
						</p>
						<p>
							<label>{LANG.type}:</label>
							<select class="small-input" name="type">
								<option value="Public">{LANG.deptypepublic}</option>
								<option value="Private">{LANG.deptypeprivate}</option>
							</select>
							<br><small>{LANG.selectprivatetype}</small>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
