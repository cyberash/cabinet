			<div class="content-box">

				<div class="content-box-header">

					<h3>Create new template</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addnotifytemplate">
					<fieldset>
						<p>
							<label>Subject:</label>
							<input class="text-input small-input" type="text" id="small-input" name="subject" value =""/>
						</p>
						<p>
							<textarea class="text-input textarea wysiwyg" id="textarea" name="text" cols="79" rows="15"></textarea>
						</p>
						<p>
							<label>Type:</label>
							<input class="text-input small-input" type="text" id="small-input" name="type" value =""/>
						</p>
						<p>
							<label>Language code:</label>
							<input class="text-input small-input" type="text" id="small-input" name="langcode" value =""/>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
