			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.paymentgateways}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: activemodulesinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noactivegatewaymodules}.
							</div>
						</div>
<!-- END: activemodulesinfo -->
<!-- BEGIN: activemodulesstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.modulesharp}</th>
								   <th>{LANG.modulename}</th>
								   <th>{LANG.status}</th>
								   <th></th>
								   <th></th>
								</tr>

							</thead>

							<tbody>
<!-- BEGIN: activemodulerow -->
								<tr>
									<td>{ACTIVEMODULE.id}</td>
									<td>{ACTIVEMODULE.modulename}</td>
									<td>{ACTIVEMODULE.status}</td>

									<form action="{PHP._SERVER.PHP_SELF}" method="post">
									<input type="hidden" name="object" value="editgateway">
									<input type="hidden" name="modulename" value="{ACTIVEMODULE.modulename}">
									<td><input class="button" type="submit" value="Edit"></td>
									</form>
									
									<form action="{PHP._SERVER.PHP_SELF}" method="post">
									<input type="hidden" name="action" value="disactivategatewaymodule">
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

					<h3>{LANG.availablegatewaymodules}</h3>

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
									<input type="hidden" name="action" value="activategatewaymodule">
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
