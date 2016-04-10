			<div class="content-box">

				<div class="content-box-header">

					<h3>Active Notification Modules</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: activemodulesinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No active modules
							</div>
						</div>
<!-- END: activemodulesinfo -->
<!-- BEGIN: activemodulesstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.modulesharp}</th>
								   <th>{LANG.modulename}</th>
								   <th></th>
								   <th></th>
								</tr>

							</thead>

							<tbody>
<!-- BEGIN: activemodulerow -->
								<tr>
									<td>{ACTIVEMODULE.id}</td>
									<td>{ACTIVEMODULE.name}</td>
									<td><a class="button" href="?object=editntmodule&moduleid={ACTIVEMODULE.id}">Edit</a></td>
								
									<form action="{PHP._SERVER.PHP_SELF}" method="post">
									<input type="hidden" name="action" value="delntmodule">
									<input type="hidden" name="moduleid" value="{ACTIVEMODULE.id}">
									<td><input class="button" type="submit" value="Disactivate"></td>
									</form>	
									
								</tr>
<!-- END: activemodulerow -->
							</tbody>
						</table>
<!-- END: activemodulestable -->

					</div>

				</div>

			</div>
			
			<div class="content-box">

				<div class="content-box-header">

					<h3>Available modules</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: availablemodulesinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noavailablegatewaymodules}
							</div>
						</div>
<!-- END: availablemodulesinfo -->
<!-- BEGIN: availablemodulesstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.modulename}</th>
								   <th>{LANG.status}</th>
								   <th></th>

								</tr>

							</thead>


							<tbody>
<!-- BEGIN: availablemodulerow -->
								<tr>
									<td>{AVAILABLEMODULENAME}</td>
									<td>{AVAILABLEMODULESTATUS}</td>
									<form action="{PHP._SERVER.PHP_SELF}" method="post">
									<input type="hidden" name="action" value="activatentmodule">
									<input type="hidden" name="modulename" value="{AVAILABLEMODULENAME}">
									<td><input class="button" type="submit" value="Activate"></td>
									</form>
								</tr>
<!-- END: availablemodulerow -->
							</tbody>
						</table>
<!-- END: availablemodulestable -->

					</div>

				</div>

			</div>
