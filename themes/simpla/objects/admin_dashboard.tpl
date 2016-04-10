				<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>{LANG.recentlyplacedorders}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->

<!-- BEGIN: ordersinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noorders}
							</div>
						</div>
<!-- END: ordersinfo -->
<!-- BEGIN: orderstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ordersharp}</th>
								   <th>{LANG.customer}</th>
								   <th>{LANG.package}</th>
								   <th>{LANG.orderstatus}</th>
								   <th>{LANG.dateordered}</th>
								   <th></th>

								</tr>

							</thead>
							<tbody>
<!-- BEGIN: orderrow -->
								<tr>
									<td><a href="?object=editorder&orderid={ORDER.id}">{ORDER.id}</a></td>
									<td><a href="?object=managecustomer&username={USERNAME}" title="Manage Customer">{USERNAME}</a></td>
									<td>{PKGNAME}</td>
									<td>{ORDER.status}</td>
									<td>{ORDER.orderdate}</td>
									<td>
										 <a href="?object=editorder&orderid={ORDER.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit" /></a>
										 <a href="?action=delorder&orderid={ORDER.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
									</td>
								</tr>
<!-- END: orderrow -->
							</tbody>
						</table>
<!-- END: orderstable -->

					</div> <!-- End #tab1 -->

				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->

			<div class="content-box column-left">

				<div class="content-box-header">

					<h3>{LANG.recentlyregisteredusers}</h3>


				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: usersinfo -->
						<div class="notification information png_bg">
							<div>
								{LANG.noactiveusers}
							</div>
						</div>
<!-- END: usersinfo -->
<!-- BEGIN: userstable -->
					<table>
						<thead>
						<tr>
								   <th>{LANG.usersharp}</th>
								   <th>{LANG.username}</th>
								   <th>{LANG.dateregistered}</th>
								   <th></th>
						</tr>
						</thead>
						<tbody>
<!-- BEGIN: userrow -->
							<tr>
								<td><a href="?object=managecustomer&userid={USER.id}" title="Manage Customer">{USER.id}</a></td>
								<td><a href="?object=managecustomer&username={USER.username}" title="Manage Customer">{USER.username}</a></td>
								<td>{USER.opentime}</td>
								<td>
										 <a href="?object=managecustomer&orderid={USER.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit" /></a>
										 <a href="?action=delcustomer&userid={USER.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
								</td>
							</tr>
<!-- END: userrow -->
						</tbody>
							
						</table>
<!-- END: userstable -->
					</div> <!-- End #tab3 -->

				</div> <!-- End .content-box-content -->


			</div> <!-- End .content-box -->

			<div class="content-box column-right ">

				<div class="content-box-header"> <!-- Add the class "closed" to the Content box header to have it closed by default -->

					<h3>{LANG.unpaidinvoices}</h3>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: invsinfo -->
						<div class="notification information png_bg">
							<div>
								{LANG.nounpaidinvoices}
							</div>
						</div>
<!-- END: invsinfo -->
<!-- BEGIN: invstable -->
						<table>
						<thead>
						<tr>
								   <th>{LANG.invoicesharp}</th>
								   <th>{LANG.customer}</th>
								   <th>{LANG.totaldue} {CURR.}</th>
								   <th>{LANG.duedate}</th>
								   <th></th>
						</tr>
						</thead>
						<tbody>
<!-- BEGIN: invrow -->
							<tr>
								<td><a href="?object=editinv&invid={INV.id}">{INV.id}</a></td>
								<td><a href="?object=managecustomer&username={USERNAME}">{USERNAME}</a></td>
								<td>{AMOUNT}</td>
								<td>{INV.datedue}</td>
								<td>
										 <a href="?object=editinv&invid={INV.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit" /></a>
										 <a href="?action=delinv&invid={INV.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
								</td>
							</tr>
<!-- END: invrow -->
						</tbody>
							
						</table>
<!-- END: invstable -->
					</div> <!-- End #tab3 -->

				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->
