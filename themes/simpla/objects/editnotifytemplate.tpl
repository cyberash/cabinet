<!-- BEGIN: wrongid -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No notifications
							</div>
						</div>
<!-- END: wrongid -->
<!-- BEGIN: content -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editnt}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="editnotifytemplate">
					<input type="hidden" name="ntid" value="{NT.id}">
					<fieldset>
						<p>
							<label>Subject:</label>
							<input class="text-input small-input" type="text" id="small-input" name="subject" value ="{NT.subject}"/>
						</p>
						<p>
							<textarea class="text-input textarea wysiwyg" id="textarea" name="text" cols="79" rows="15">{NT.text}</textarea>
						</p>
						<p>
							<label>Type:</label>
							<input class="text-input small-input" type="text" id="small-input" name="type" value ="{NT.type}"/>
						</p>
						<p>
							<label>Language code:</label>
							<input class="text-input small-input" type="text" id="small-input" name="langcode" value ="{NT.langcode}"/>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
<!-- END: content -->
