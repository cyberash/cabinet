<!-- BEGIN: depinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.currentlynodepartments}
							</div>
						</div>
<!-- END: depinfo -->
<!-- BEGIN: ticket -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.newticket}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addticket">
					<fieldset>
						<p>
							<label>{LANG.subject}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="subject" />
						</p>
						<p>
							<textarea class="text-input textarea wysiwyg" id="textarea" name="message" cols="79" rows="15"></textarea>
						</p>
						<p>
							<label>{LANG.department}:</label>
							<select class="small-input" name="depid">
<!-- BEGIN: deprow -->
								<option value="{DEP.id}">{DEP.name}</option>
<!-- END: deprow -->
							</select>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
<!-- END: ticket -->
