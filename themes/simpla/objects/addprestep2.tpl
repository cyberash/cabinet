			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.addnewpresetstep2}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addprestep3" />
					<input type="hidden" name="groupid" value="{GROUPID}" />
					<fieldset>
						<p>
							<label>{LANG.presetname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="name" />
						</p>
<!-- BEGIN: inputtext -->
						<p>
							<label>{INPUT.label}</label>
							<input class="text-input small-input" type="{INPUT.type}" id="small-input" name="{INPUT.name}" />
						</p>
<!-- END: inputtext -->
<!-- BEGIN: checkbox -->
						<p>
							<label>{INPUT.label}</label>
							<input class="text-input small-input" type="{INPUT.type}" id="small-input" name="{INPUT.name}" value="{INPUT.name}" />
						</p>
<!-- END: checkbox -->
<!-- BEGIN: select -->
						<p>
							<label>{INPUT.label}</label>
							<select class="small-input" name="{INPUT.name}">
<!-- BEGIN: option -->
								<option value="{OPTIONNAME}">{OPTIONLABEL}</option>
<!-- END: option -->
							</select>
						</p>
<!-- END: select -->
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
