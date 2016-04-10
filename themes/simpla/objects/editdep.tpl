<!-- BEGIN: depinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.departmentidnotset}
							</div>
						</div>
<!-- END: depinfo -->
<!-- BEGIN: depbox -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editdepartemnt}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="updatedep">
					<input type="hidden" name="depid" value="{DEP.id}">
					<fieldset>
						<p>
							<label>{LANG.departmentname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="depname" value ="{DEP.name}"/>
						</p>
						<p>
							<label>{LANG.type}:</label>
							<select class="small-input" name="type">
								<option value="Public" {DEFPublic}>{LANG.deptypepublic}</option>
								<option value="Private" {DEFPrivate}>{LANG.deptypeprivate}</option>
							</select>
							<br><small>{LANG.selectprivatetype}.</small>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
<!-- END: depbox -->
